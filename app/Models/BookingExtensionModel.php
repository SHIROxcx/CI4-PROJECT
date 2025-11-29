<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingExtensionModel extends Model
{
    protected $table = 'booking_extensions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'booking_id',
        'extension_hours',
        'extension_cost',
        'extension_reason',
        'status',
        'requested_by',
        'requested_by_id',
        'requested_at',
        'approved_by',
        'approved_at',
        'payment_status',
        'payment_order_generated',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Request a new hour extension for a booking
     * Called when student/faculty wants to extend
     */
    public function requestExtension($bookingId, $extensionHours, $requestedById, $requestedByName, $reason = '')
    {
        try {
            // Get booking details
            $bookingModel = new BookingModel();
            $booking = $bookingModel->find($bookingId);

            if (!$booking) {
                throw new \Exception('Booking not found');
            }

            // Get facility hourly rate
            $facilityModel = new FacilityModel();
            $facility = $facilityModel->find($booking['facility_id']);

            if (!$facility) {
                throw new \Exception('Facility not found');
            }

            // Calculate extension cost based on additional hours rate
            $hourlyRate = $facility['additional_hours_rate'] ?? 0;
            if ($hourlyRate <= 0) {
                throw new \Exception('Facility additional hours rate not configured');
            }

            $extensionCost = $extensionHours * $hourlyRate;

            $extensionData = [
                'booking_id' => $bookingId,
                'extension_hours' => $extensionHours,
                'extension_cost' => $extensionCost,
                'extension_reason' => $reason,
                'requested_by_id' => $requestedById,
                'requested_by' => $requestedByName,
                'requested_at' => date('Y-m-d H:i:s'),
                'status' => 'pending',
                'payment_status' => 'pending',
            ];

            if ($this->insert($extensionData)) {
                return [
                    'success' => true,
                    'extension_id' => $this->getInsertID(),
                    'message' => 'Extension request submitted successfully',
                    'extension_cost' => $extensionCost,
                    'hourly_rate' => $hourlyRate,
                ];
            }

            throw new \Exception('Failed to create extension request');
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get pending extensions for a specific facility
     */
    public function getPendingExtensionsByFacility($facilityId, $limit = 50, $offset = 0)
    {
        return $this->db->table('booking_extensions be')
        ->select('
            be.*,
            b.id as booking_id,
            b.client_name,
            b.email_address,
            b.event_title,
            b.event_date,
            b.event_time,
            b.total_cost as booking_total_cost,
            f.name as facility_name,
            u.full_name as requested_by_full_name
        ')
        ->join('bookings b', 'b.id = be.booking_id', 'left')
        ->join('facilities f', 'f.id = b.facility_id', 'left')
        ->join('users u', 'u.id = be.requested_by_id', 'left')
        ->where('b.facility_id', $facilityId)
        ->where('be.status', 'pending')
        ->orderBy('be.requested_at', 'DESC')
        ->limit($limit, $offset)
        ->get()
        ->getResultArray();
    }

    /**
     * Get all pending extensions (for admin)
     */
    public function getAllPendingExtensions($limit = 50, $offset = 0)
    {
        return $this->db->table('booking_extensions be')
        ->select('
            be.*,
            b.id as booking_id,
            b.client_name,
            b.email_address,
            b.event_title,
            b.event_date,
            b.total_cost as booking_total_cost,
            f.name as facility_name,
            u.full_name as requested_by_full_name
        ')
        ->join('bookings b', 'b.id = be.booking_id', 'left')
        ->join('facilities f', 'f.id = b.facility_id', 'left')
        ->join('users u', 'u.id = be.requested_by_id', 'left')
        ->where('be.status', 'pending')
        ->orderBy('be.requested_at', 'DESC')
        ->limit($limit, $offset)
        ->get()
        ->getResultArray();
    }

    /**
     * Get extension details with booking and files
     */
    public function getExtensionWithDetails($extensionId)
    {
        $extension = $this->db->table('booking_extensions be')
        ->select('
            be.*,
            b.id as booking_id,
            b.client_name,
            b.email_address,
            b.event_title,
            b.event_date,
            b.event_time,
            b.total_cost as booking_total_cost,
            b.facility_id,
            f.name as facility_name,
            f.additional_hours_rate as hourly_rate,
            u.full_name as requested_by_full_name
        ')
        ->join('bookings b', 'b.id = be.booking_id', 'left')
        ->join('facilities f', 'f.id = b.facility_id', 'left')
        ->join('users u', 'u.id = be.requested_by_id', 'left')
        ->where('be.id', $extensionId)
        ->get()
        ->getRowArray();

        if ($extension) {
            // Get associated files
            $extensionFileModel = new ExtensionFileModel();
            $extension['files'] = $extensionFileModel->where('extension_id', $extensionId)
                ->where('status', 'active')
                ->findAll();
        }

        return $extension;
    }

    /**
     * Get extension by booking ID
     */
    public function getByBookingId($bookingId)
    {
        return $this->db->table('booking_extensions be')
        ->select('
            be.*,
            b.id as booking_id,
            b.client_name,
            b.email_address,
            f.name as facility_name,
            u.full_name as requested_by_full_name
        ')
        ->join('bookings b', 'b.id = be.booking_id', 'left')
        ->join('facilities f', 'f.id = b.facility_id', 'left')
        ->join('users u', 'u.id = be.requested_by_id', 'left')
        ->where('be.booking_id', $bookingId)
        ->get()
        ->getRowArray();
    }

    /**
     * Approve extension and update booking
     */
    public function approveExtension($extensionId, $approvedById)
    {
        try {
            $extension = $this->find($extensionId);

            if (!$extension) {
                throw new \Exception('Extension request not found');
            }

            if ($extension['status'] !== 'pending') {
                throw new \Exception('Only pending extensions can be approved');
            }

            // Update extension status
            $this->update($extensionId, [
                'status' => 'approved',
                'approved_by' => $approvedById,
                'approved_at' => date('Y-m-d H:i:s'),
            ]);

            // Update booking with additional hours
            $bookingModel = new BookingModel();
            $booking = $bookingModel->find($extension['booking_id']);

            if ($booking) {
                $currentAdditionalHours = $booking['additional_hours'] ?? 0;
                $newAdditionalHours = $currentAdditionalHours + $extension['extension_hours'];

                $bookingModel->update($extension['booking_id'], [
                    'additional_hours' => $newAdditionalHours,
                    'total_cost' => $booking['total_cost'] + $extension['extension_cost'],
                ]);

                return [
                    'success' => true,
                    'message' => 'Extension approved successfully',
                ];
            }

            throw new \Exception('Failed to update booking');
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Reject extension request
     */
    public function rejectExtension($extensionId, $rejectReason = '')
    {
        try {
            $extension = $this->find($extensionId);

            if (!$extension) {
                throw new \Exception('Extension request not found');
            }

            $this->update($extensionId, [
                'status' => 'rejected',
                'extension_reason' => $extension['extension_reason'] . ' [REJECTED: ' . $rejectReason . ']',
            ]);

            return [
                'success' => true,
                'message' => 'Extension rejected',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mark payment as received
     */
    public function markPaymentReceived($extensionId)
    {
        try {
            $extension = $this->find($extensionId);

            if (!$extension) {
                throw new \Exception('Extension request not found');
            }

            $this->update($extensionId, [
                'payment_status' => 'received',
                'status' => 'completed',
            ]);

            return [
                'success' => true,
                'message' => 'Payment marked as received',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mark payment order as generated
     */
    public function markPaymentOrderGenerated($extensionId)
    {
        try {
            $this->update($extensionId, [
                'payment_order_generated' => true,
            ]);

            return [
                'success' => true,
                'message' => 'Payment order marked as generated',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get extension statistics
     */
    public function getExtensionStats($facilityId = null)
    {
        $query = $this->select('
            COUNT(*) as total_requests,
            SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = "completed" THEN extension_cost ELSE 0 END) as total_completed_revenue,
            SUM(extension_cost) as total_extension_revenue
        ');

        if ($facilityId) {
            $query->join('bookings b', 'b.id = booking_id', 'left')
                ->where('b.facility_id', $facilityId);
        }

        return $query->first();
    }

    /**
     * Count pending extensions for a facility
     */
    public function countPendingByFacility($facilityId)
    {
        return $this->select('COUNT(*) as count')
            ->join('bookings b', 'b.id = booking_id', 'left')
            ->where('b.facility_id', $facilityId)
            ->where('status', 'pending')
            ->first()['count'] ?? 0;
    }

    /**
     * Get extensions for a user (student/faculty view)
     */
    public function getUserExtensions($userId, $limit = 50, $offset = 0)
    {
        return $this->select('
            be.*,
            b.id as booking_id,
            b.event_title,
            b.event_date,
            f.name as facility_name
        ')
        ->join('bookings b', 'b.id = be.booking_id', 'left')
        ->join('facilities f', 'f.id = b.facility_id', 'left')
        ->where('be.requested_by_id', $userId)
        ->orderBy('be.requested_at', 'DESC')
        ->limit($limit, $offset)
        ->get()
        ->getResultArray();
    }
}
