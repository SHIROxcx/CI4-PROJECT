<?php

namespace App\Controllers\Api;

use App\Models\BookingModel;
use App\Models\FacilityModel;
use App\Models\PlanModel;
use App\Models\AddonModel;
use App\Models\BookingAddonModel;
use App\Models\BookingFileModel;
use App\Models\BookingEquipmentModel;
use App\Models\EquipmentModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use App\Models\StudentBookingFileModel;

class StudentBookingApi extends ResourceController
{
    protected $bookingModel;
    protected $facilityModel;
    protected $planModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->facilityModel = new FacilityModel();
        $this->planModel = new PlanModel();
    }

    /**
     * Verify user access - Allow both students and admins
     */
private function verifyUserAccess()
{
    $session = session();
    $userId = $session->get('user_id');
    $userRole = $session->get('role');
    $userEmail = $session->get('email');

    // Allow guest bookings (non-logged-in users)
    if (!$userId || !$userEmail) {
        return null; // Return null instead of false for guests
    }

    // Allow students, faculty, and admins
    if (!in_array($userRole, ['student', 'faculty', 'admin'])) {
        return null;
    }

    return [
        'user_id' => $userId,
        'role' => $userRole,
        'email' => $userEmail,
        'full_name' => $session->get('full_name')
    ];
}

    /**
     * Create student booking
     * Accessible by both students and admins
     */
public function createStudentBooking()
{
    try {
        $userData = $this->verifyUserAccess();
        // Note: $userData can be null for guest bookings, which is allowed

        $request = $this->request->getJSON(true);
        
        // Validate required fields
        $validation = \Config\Services::validation();
$validation->setRules([
    'facility_id' => 'required|integer',
    'plan_id' => 'required|integer',
    'client_name' => 'required|min_length[3]|max_length[255]',
    'email_address' => 'required|valid_email|max_length[255]',
    'organization' => 'required|min_length[3]|max_length[255]',
    'contact_number' => 'required|min_length[7]|max_length[20]',
    'address' => 'permit_empty|min_length[10]|max_length[500]',
    'event_date' => 'required|valid_date',
    'event_time' => 'required',
    'duration' => 'required|integer',
    'attendees' => 'permit_empty|integer',
    'event_title' => 'required|min_length[5]|max_length[255]'
], [
    'client_name' => [
        'required' => 'Full name is required',
        'min_length' => 'Full name must be at least 3 characters'
    ],
    'organization' => [
        'required' => 'Organization name is required',
        'min_length' => 'Organization name must be at least 3 characters'
    ],
    'contact_number' => [
        'required' => 'Contact number is required',
        'min_length' => 'Contact number must be at least 7 digits'
    ],
    'address' => [
        'min_length' => 'Address must be at least 10 characters (include street, city, province)'
    ],
    'event_title' => [
        'required' => 'Event title is required',
        'min_length' => 'Event title must be at least 5 characters'
    ]
]);

        if (!$validation->run($request)) {
            log_message('error', 'Validation failed: ' . json_encode($validation->getErrors()));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ])->setStatusCode(400);
        }

        // Always use form data for client name and email
        $clientName = $request['client_name'];
        $emailAddress = $request['email_address'];

        // Determine created_by based on login status
        $createdBy = $userData ? $userData['user_id'] : null;

        // Get facility details
        $facility = $this->facilityModel->find($request['facility_id']);
        if (!$facility) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Facility not found'
            ])->setStatusCode(404);
        }

        // Get plan details
        $plan = $this->planModel->find($request['plan_id']);
        if (!$plan) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Plan not found'
            ])->setStatusCode(404);
        }

        // Check facility availability
        $isAvailable = $this->bookingModel->checkFacilityAvailability(
            $request['facility_id'],
            $request['event_date'],
            $request['event_time'],
            $request['duration']
        );

        if (!$isAvailable) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Facility is not available at the selected date and time'
            ])->setStatusCode(409);
        }

        // Calculate total cost
        // For student bookings, total cost is always 0 (free for students)
        $totalCost = 0;
        $additionalHours = $request['additional_hours'] ?? 0;

        // Prepare booking data
        $bookingData = [
            'facility_id' => $request['facility_id'],
            'plan_id' => $request['plan_id'],
            'client_name' => $clientName,
            'contact_number' => $request['contact_number'],
            'email_address' => $emailAddress,
            'organization' => $request['organization'],
            'address' => $request['address'] ?? null,
            'event_date' => $request['event_date'],
            'event_time' => $request['event_time'],
            'duration' => $request['duration'],
            'attendees' => $request['attendees'] ?? null,
            'event_title' => $request['event_title'],
            'special_requirements' => $request['special_requirements'] ?? '',
            'total_cost' => $totalCost,
            'additional_hours' => $additionalHours,
            'booking_type' => 'student',
            'status' => 'pending',
            'created_by' => $createdBy
        ];

        // Start transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Insert booking
        $bookingId = $this->bookingModel->insert($bookingData);

        if (!$bookingId) {
            $db->transRollback();
            log_message('error', 'Failed to insert booking: ' . json_encode($this->bookingModel->errors()));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create booking',
                'errors' => $this->bookingModel->errors()
            ])->setStatusCode(500);
        }

        // Handle equipment if provided (date-based availability system)
if (!empty($request['selected_equipment'])) {
    $bookingEquipmentModel = new BookingEquipmentModel();
    $equipmentModel = new EquipmentModel();
    $eventDate = $request['event_date'];

    foreach ($request['selected_equipment'] as $equipmentId => $quantity) {
        if ($quantity > 0) {
            // Check if equipment exists
            $equipment = $equipmentModel->find($equipmentId);

            if (!$equipment) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Equipment not found: ID ' . $equipmentId
                ])->setStatusCode(404);
            }

            // Calculate date-based availability for this equipment
            $bookedQuery = $db->table('booking_equipment be')
                ->select('SUM(be.quantity) as booked_quantity')
                ->join('bookings b', 'b.id = be.booking_id', 'inner')
                ->where('be.equipment_id', $equipmentId)
                ->where('b.event_date', $eventDate)
                ->whereIn('b.status', ['pending', 'confirmed', 'approved'])
                ->get();

            $bookedResult = $bookedQuery->getRowArray();
            $bookedQuantity = (int)($bookedResult['booked_quantity'] ?? 0);
            $availableForDate = $equipment['quantity'] - $bookedQuantity;

            if ($availableForDate < $quantity) {
                $db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Insufficient {$equipment['name']} available on {$eventDate}. Requested: {$quantity}, Available: {$availableForDate}"
                ])->setStatusCode(400);
            }

            // Get equipment rate for cost calculation
            $equipmentRate = $equipment['rate'];
            $equipmentCost = $equipmentRate * $quantity;

            // Insert booking equipment record
            $bookingEquipmentModel->insert([
                'booking_id' => $bookingId,
                'equipment_id' => $equipmentId,
                'quantity' => $quantity,
                'rate' => $equipmentRate,
                'total_cost' => $equipmentCost
            ]);

            // No global inventory deduction - availability is calculated per date
        }
    }
}

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Transaction failed for booking creation');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to complete booking transaction'
            ])->setStatusCode(500);
        }

        // Fixed logging line
        $bookingType = $userData ? $userData['role'] : 'guest';
        log_message('info', "Student booking created successfully: {$bookingId} by {$bookingType} {$emailAddress}");

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Booking created successfully',
            'booking_id' => $bookingId,
            'reference_number' => 'BK' . str_pad($bookingId, 3, '0', STR_PAD_LEFT)
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Create student booking error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create booking: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}

    /**
     * Get all student bookings
     * Students see only their bookings, admins see all student bookings
     */
    public function getStudentBookings()
    {
        try {
            $userData = $this->verifyUserAccess();
            
            if (!$userData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(401);
            }

            $builder = $this->bookingModel
                ->select('bookings.*, facilities.name as facility_name, facilities.facility_key, plans.name as plan_name')
                ->join('facilities', 'facilities.id = bookings.facility_id')
                ->join('plans', 'plans.id = bookings.plan_id')
                ->where('bookings.booking_type', 'student');

            // If student, show only their bookings
            if ($userData['role'] === 'student') {
                $builder->where('bookings.email_address', $userData['email']);
            }

            $bookings = $builder
                ->orderBy('bookings.created_at', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'bookings' => $bookings
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get student bookings error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch bookings'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get single student booking details
     */
    public function getStudentBooking($bookingId)
    {
        try {
            $userData = $this->verifyUserAccess();
            
            if (!$userData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(401);
            }

            $booking = $this->bookingModel->getBookingWithFullDetails($bookingId);

            if (!$booking) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Booking not found'
                ])->setStatusCode(404);
            }

            // Verify access: students can only see their own bookings
            if ($userData['role'] === 'student' && $booking['email_address'] !== $userData['email']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }

            return $this->response->setJSON([
                'success' => true,
                'booking' => $booking
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get student booking error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to fetch booking details'
            ])->setStatusCode(500);
        }
    }

    /**
     * Upload student documents
     */
public function uploadStudentDocuments($bookingId)
{
    try {
        // Verify booking exists
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Booking not found'
            ])->setStatusCode(404);
        }

        // Verify it's a student booking
        if ($booking['booking_type'] !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not a student booking'
            ])->setStatusCode(400);
        }

        $uploadPath = WRITEPATH . 'uploads/student_booking_files/' . $bookingId . '/';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $uploadedFiles = [];
        
        // Get files from request - FIXED THIS PART
        $files = $this->request->getFiles();
        
        if (empty($files) || !isset($files['files'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No files uploaded'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        
        // Map array index to file types
        $fileTypeMapping = [
            0 => 'permission_letter',
            1 => 'request_letter', 
            2 => 'approval_letter'
        ];
        
        // Process each uploaded file
// Process each uploaded file
foreach ($files['files'] as $index => $file) {
    // CRITICAL: Check if file is an array (when multiple files with same name)
    if (is_array($file)) {
        $file = $file[0]; // Take first file
    }
    
    if ($file->isValid() && !$file->hasMoved()) {
        
        // Validate file size
        if ($file->getSize() > 10 * 1024 * 1024) {
            log_message('warning', "File too large: {$file->getClientName()}");
            continue;
        }

        // Get file type from mapping
        $fileType = $fileTypeMapping[$index] ?? 'document';
        
        // Check if file already exists for this type
        $existingFile = $db->table('student_booking_files')
                          ->where('booking_id', $bookingId)
                          ->where('file_type', $fileType)
                          ->get()
                          ->getRowArray();

        // Delete old file if exists
        if ($existingFile) {
            if (file_exists($existingFile['file_path'])) {
                unlink($existingFile['file_path']);
            }
            $db->table('student_booking_files')
               ->where('id', $existingFile['id'])
               ->delete();
        }

        // Generate new filename
        $newName = $file->getRandomName();
        
        // Move file
        if ($file->move($uploadPath, $newName)) {
            $fileData = [
                'booking_id' => $bookingId,
                'file_type' => $fileType,
                'original_filename' => $file->getClientName(),
                'stored_filename' => $newName,
                'file_path' => $uploadPath . $newName,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getClientMimeType(),
                'upload_date' => date('Y-m-d H:i:s')
            ];
            
            $db->table('student_booking_files')->insert($fileData);
            
            $uploadedFiles[] = [
                'id' => $db->insertID(),
                'file_type' => $fileType,
                'filename' => $file->getClientName(),
                'size' => $file->getSize()
            ];
            
            log_message('info', "File uploaded: {$file->getClientName()} as {$fileType}");
        }
    }
}

        if (empty($uploadedFiles)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No files were successfully uploaded'
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => count($uploadedFiles) . ' file(s) uploaded successfully',
            'files' => $uploadedFiles
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Upload error: ' . $e->getMessage());
        log_message('error', 'Stack trace: ' . $e->getTraceAsString());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to upload files: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}
    /**
     * Get student booking files
     */
public function getStudentBookingFiles($bookingId)
{
              try {
        // Allow both authenticated users and admins to view files
        $session = session();
        $userRole = $session->get('role');
        
        // Admins can always view
        if ($userRole !== 'admin') {
            $userData = $this->verifyUserAccess();
            
            if (!$userData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(401);
            }

            // Verify booking exists and user has access
            $booking = $this->bookingModel->find($bookingId);
            if (!$booking) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Booking not found'
                ])->setStatusCode(404);
            }

            // Students can only see their own bookings
            if ($userData['role'] === 'student' && $booking['email_address'] !== $userData['email']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }
        }

        // Use StudentBookingFileModel instead of BookingFileModel
        $studentFileModel = new StudentBookingFileModel();
        $files = $studentFileModel->where('booking_id', $bookingId)->findAll();

        // Format file data for response
        $formattedFiles = array_map(function($file) {
            return [
                'id' => $file['id'],
                'file_type' => $file['file_type'],
                'filename' => $file['original_filename'],
                'size' => $file['file_size'],
                'mime_type' => $file['mime_type'],
                'upload_date' => $file['upload_date'],
                'size_formatted' => $this->formatFileSize($file['file_size'])
            ];
        }, $files);

        return $this->response->setJSON([
            'success' => true,
            'files' => $formattedFiles,
            'total_files' => count($formattedFiles)
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'Get files error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to get files'
        ])->setStatusCode(500);
    }
}


    /**
     * Download student document
     */

public function downloadStudentDocument($bookingId, $fileId)
{
    try {
        $userData = $this->verifyUserAccess();
        
        if (!$userData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ])->setStatusCode(401);
        }

        // Verify booking exists and user has access
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Booking not found'
            ])->setStatusCode(404);
        }

        // Verify access: students can only download from their own bookings
        if ($userData['role'] === 'student' && $booking['email_address'] !== $userData['email']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        // Use StudentBookingFileModel
        $studentFileModel = new StudentBookingFileModel();
        $file = $studentFileModel->where('booking_id', $bookingId)
                                 ->where('id', $fileId)
                                 ->first();
        
        if (!$file || !file_exists($file['file_path'])) {
            log_message('warning', "File not found: Booking {$bookingId}, File {$fileId}");
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File not found'
            ])->setStatusCode(404);
        }

        log_message('info', "File download: {$file['original_filename']} by user {$userData['email']}");

        return $this->response->download($file['file_path'], null)
                             ->setFileName($file['original_filename']);

    } catch (\Exception $e) {
        log_message('error', 'Download failed: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Download failed'
        ])->setStatusCode(500);
    }
}

    /**
     * Delete student document
     */
public function deleteStudentDocument($bookingId, $fileId)
{
    try {
        $userData = $this->verifyUserAccess();
        
        if (!$userData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ])->setStatusCode(401);
        }

        // Verify booking exists and user has access
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Booking not found'
            ])->setStatusCode(404);
        }

        // Verify access: students can only delete from their own bookings
        if ($userData['role'] === 'student' && $booking['email_address'] !== $userData['email']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        // Only allow deletion if booking is still pending
        if ($booking['status'] !== 'pending') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete files from non-pending bookings'
            ])->setStatusCode(400);
        }

        // Use StudentBookingFileModel
        $studentFileModel = new StudentBookingFileModel();
        $file = $studentFileModel->where('booking_id', $bookingId)
                                 ->where('id', $fileId)
                                 ->first();
        
        if (!$file) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File not found'
            ])->setStatusCode(404);
        }

        // Delete physical file
        if (file_exists($file['file_path'])) {
            unlink($file['file_path']);
        }

        // Delete database record
        if ($studentFileModel->delete($fileId)) {
            log_message('info', "File deleted: {$file['original_filename']} from booking {$bookingId}");
            return $this->response->setJSON([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
        } else {
            throw new \Exception('Failed to delete file from database');
        }

    } catch (\Exception $e) {
        log_message('error', 'Delete file error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to delete file'
        ])->setStatusCode(500);
    }
}

    /**
     * Cancel student booking
     */
    public function cancelStudentBooking($bookingId)
    {
        try {
            $userData = $this->verifyUserAccess();
            
            if (!$userData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(401);
            }

            // Verify booking exists and user has access
            $booking = $this->bookingModel->find($bookingId);
            if (!$booking) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Booking not found'
                ])->setStatusCode(404);
            }

            // Verify access: students can only cancel their own bookings
            if ($userData['role'] === 'student' && $booking['email_address'] !== $userData['email']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }

            // Check if booking can be cancelled
            if (!in_array($booking['status'], ['pending', 'approved', 'confirmed'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This booking cannot be cancelled'
                ])->setStatusCode(400);
            }

            // Get cancellation details from request
            $reason = $this->request->getPost('reason');
            $notes = $this->request->getPost('notes');

            if (!$reason) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cancellation reason is required'
                ])->setStatusCode(400);
            }

            // Handle cancellation letter file upload
            $cancelLetterPath = null;
            $file = $this->request->getFile('cancel_letter');

            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Validate file size (10MB)
                if ($file->getSize() > 10 * 1024 * 1024) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Cancellation letter must be less than 10MB'
                    ])->setStatusCode(400);
                }

                // Validate file type
                $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Only PDF, JPG, and PNG files are allowed'
                    ])->setStatusCode(400);
                }

                // Create cancellations directory if it doesn't exist
                $uploadDir = WRITEPATH . 'uploads/cancellations';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Move file to uploads directory
                $newName = $file->getRandomName();
                $file->move($uploadDir, $newName);
                $cancelLetterPath = 'cancellations/' . $newName;
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Cancellation letter is required'
                ])->setStatusCode(400);
            }

            $db = \Config\Database::connect();
            $db->transStart();

            // Update booking status with cancellation details
            $this->bookingModel->update($bookingId, [
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancellation_notes' => $notes,
                'cancellation_letter_path' => $cancelLetterPath,
                'cancelled_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Date-based availability system: No equipment restoration needed
            // Equipment availability is calculated dynamically per date
            // Cancelled bookings are excluded from availability calculations

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Failed to cancel booking');
            }

            log_message('info', "Booking cancelled: {$bookingId} by user {$userData['email']}, Reason: {$reason}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Booking cancelled successfully. Your cancellation letter has been received and submitted to the office for review.'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Cancel booking error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to cancel booking'
            ])->setStatusCode(500);
        }
    }

    /**
     * Update student booking (only for pending bookings)
     */
    public function updateStudentBooking($bookingId)
    {
        try {
            $userData = $this->verifyUserAccess();
            
            if (!$userData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(401);
            }

            // Verify booking exists and user has access
            $booking = $this->bookingModel->find($bookingId);
            if (!$booking) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Booking not found'
                ])->setStatusCode(404);
            }

            // Verify access: students can only update their own bookings
            if ($userData['role'] === 'student' && $booking['email_address'] !== $userData['email']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied'
                ])->setStatusCode(403);
            }

            // Only allow updates for pending bookings
            if ($booking['status'] !== 'pending') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only pending bookings can be updated'
                ])->setStatusCode(400);
            }

            $request = $this->request->getJSON(true);

            // Validate updatable fields
            $validation = \Config\Services::validation();
            $validation->setRules([
                'contact_number' => 'permit_empty|max_length[20]',
                'organization' => 'permit_empty|max_length[255]',
                'address' => 'permit_empty|max_length[500]',
                'event_date' => 'permit_empty|valid_date',
                'event_time' => 'permit_empty',
                'attendees' => 'permit_empty|integer',
                'event_title' => 'permit_empty|max_length[255]',
                'special_requirements' => 'permit_empty'
            ]);

            if (!$validation->run($request)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            }

            // Check availability if date/time changed
            if (isset($request['event_date']) || isset($request['event_time'])) {
                $newDate = $request['event_date'] ?? $booking['event_date'];
                $newTime = $request['event_time'] ?? $booking['event_time'];
                
                $isAvailable = $this->bookingModel->checkFacilityAvailability(
                    $booking['facility_id'],
                    $newDate,
                    $newTime,
                    $booking['duration'],
                    $bookingId // Exclude current booking
                );

                if (!$isAvailable) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Facility is not available at the selected date and time'
                    ])->setStatusCode(409);
                }
            }

            // Prepare update data
            $updateData = array_filter([
                'contact_number' => $request['contact_number'] ?? null,
                'organization' => $request['organization'] ?? null,
                'address' => $request['address'] ?? null,
                'event_date' => $request['event_date'] ?? null,
                'event_time' => $request['event_time'] ?? null,
                'attendees' => $request['attendees'] ?? null,
                'event_title' => $request['event_title'] ?? null,
                'special_requirements' => $request['special_requirements'] ?? null,
                'updated_at' => date('Y-m-d H:i:s')
            ], function($value) {
                return $value !== null;
            });

            if (empty($updateData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No valid fields to update'
                ])->setStatusCode(400);
            }

            if ($this->bookingModel->update($bookingId, $updateData)) {
                log_message('info', "Booking updated: {$bookingId} by user {$userData['email']}");
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Booking updated successfully'
                ]);
            } else {
                throw new \Exception('Failed to update booking');
            }

        } catch (\Exception $e) {
            log_message('error', 'Update booking error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update booking'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get booking statistics for student
     */
    public function getStudentStatistics()
    {
        try {
            $userData = $this->verifyUserAccess();
            
            if (!$userData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ])->setStatusCode(401);
            }

            $builder = $this->bookingModel
                ->where('booking_type', 'student');

            // If student, show only their bookings
            if ($userData['role'] === 'student') {
                $builder->where('email_address', $userData['email']);
            }

            $bookings = $builder->findAll();

            $statistics = [
                'total_bookings' => count($bookings),
                'pending' => 0,
                'approved' => 0,
                'completed' => 0,
                'cancelled' => 0,
                'rejected' => 0,
                'upcoming_bookings' => 0,
                'past_bookings' => 0
            ];

            $today = date('Y-m-d');

            foreach ($bookings as $booking) {
                // Count by status
                $status = strtolower($booking['status']);
                if (isset($statistics[$status])) {
                    $statistics[$status]++;
                }

                // Count upcoming vs past
                if ($booking['event_date'] >= $today) {
                    $statistics['upcoming_bookings']++;
                } else {
                    $statistics['past_bookings']++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get statistics error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get statistics'
            ])->setStatusCode(500);
        }
    }

    /**
     * Format file size to human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

public function getFacilityData($facilityKey)
{
    try {
        // Check if user is logged in
        $session = session();
        $userId = $session->get('user_id');
        
        // User must be logged in to access this
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ])->setStatusCode(401);
        }

        // Get facility by key
        $facility = $this->facilityModel->where('facility_key', $facilityKey)->first();
        
        if (!$facility) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Facility not found'
            ])->setStatusCode(404);
        }

        // Get plans for this facility
        $plans = $this->planModel->where('facility_id', $facility['id'])->findAll();
        
        if (!$plans) {
            $plans = [];
        }
        
        $facility['plans'] = $plans;

        return $this->response->setJSON([
            'success' => true,
            'facility' => $facility
        ])->setStatusCode(200);

    } catch (\Exception $e) {
        log_message('error', 'Get facility data error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to fetch facility data: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}

/**
 * Get available equipment
 */
public function getEquipment()
{
    try {
        $userData = $this->verifyUserAccess();

        if (!$userData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ])->setStatusCode(401);
        }

        $equipmentModel = new EquipmentModel();
        $eventDate = $this->request->getGet('event_date');

        // If event date is provided, calculate date-based availability
        if ($eventDate) {
            $db = \Config\Database::connect();

            // Get all trackable equipment
            $equipment = $equipmentModel
                ->where('is_trackable', 1)
                ->findAll();

            // Calculate availability for each equipment on the specified date
            $equipmentAvailability = [];
            foreach ($equipment as $item) {
                // Get quantity booked on this date
                $bookedQuery = $db->table('booking_equipment be')
                    ->select('SUM(be.quantity) as booked_quantity')
                    ->join('bookings b', 'b.id = be.booking_id', 'inner')
                    ->where('be.equipment_id', $item['id'])
                    ->where('b.event_date', $eventDate)
                    ->whereIn('b.status', ['pending', 'confirmed', 'approved'])
                    ->get();

                $bookedResult = $bookedQuery->getRowArray();
                $bookedQuantity = (int)($bookedResult['booked_quantity'] ?? 0);

                // Calculate available quantity for this date
                $availableQuantity = max(0, $item['quantity'] - $bookedQuantity);

                $equipmentAvailability[] = [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'category' => $item['category'],
                    'quantity' => $item['quantity'],
                    'available' => $availableQuantity,
                    'available_quantity' => $availableQuantity,
                    'booked_quantity' => $bookedQuantity,
                    'rate' => $item['rate'],
                    'unit' => $item['unit'],
                    'is_trackable' => $item['is_trackable']
                ];
            }

            return $this->response->setJSON([
                'success' => true,
                'equipment' => $equipmentAvailability,
                'filtered_by_date' => true,
                'event_date' => $eventDate
            ]);
        } else {
            // Return equipment with global availability (for backward compatibility)
            $equipment = $equipmentModel
                ->where('is_trackable', 1)
                ->where('available >', 0)
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'equipment' => $equipment,
                'filtered_by_date' => false
            ]);
        }

    } catch (\Exception $e) {
        log_message('error', 'Get equipment error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to fetch equipment'
        ])->setStatusCode(500);
    }
}
public function getStudentBookingFilesForAdmin($bookingId)
{
    try {
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Booking not found'
            ])->setStatusCode(404);
        }

        // Check if it's a student booking
        if ($booking['booking_type'] !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not a student booking'
            ])->setStatusCode(400);
        }

        $studentFileModel = new StudentBookingFileModel();
        $files = $studentFileModel->where('booking_id', $bookingId)->findAll();

        // Format file data
        $formattedFiles = array_map(function($file) {
            return [
                'id' => $file['id'],
                'file_type' => $file['file_type'],
                'original_filename' => $file['original_filename'],
                'file_size' => $file['file_size'],
                'mime_type' => $file['mime_type'],
                'upload_date' => $file['upload_date']
            ];
        }, $files);

        return $this->response->setJSON([
            'success' => true,
            'files' => $formattedFiles
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Get student files error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to get files'
        ])->setStatusCode(500);
    }
}

/**
 * Get equipment availability for a specific date
 * Shows how many units of each equipment are available on that date
 */
public function getEquipmentAvailabilityByDate()
{
    try {
        $userData = $this->verifyUserAccess();
        
        if (!$userData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ])->setStatusCode(401);
        }

        $eventDate = $this->request->getGet('event_date');
        
        if (!$eventDate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Event date is required'
            ])->setStatusCode(400);
        }

        // Validate date format
        if (!strtotime($eventDate)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid date format'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $equipmentModel = new EquipmentModel();

        // Get all trackable equipment
        $equipment = $equipmentModel
            ->where('is_trackable', 1)
            ->findAll();

        if (empty($equipment)) {
            return $this->response->setJSON([
                'success' => true,
                'equipment' => [],
                'message' => 'No equipment available'
            ]);
        }

        $equipmentAvailability = [];

        foreach ($equipment as $item) {
            // Get total quantity of this equipment
            $totalQuantity = $item['quantity'];

            // Get quantity booked on this date (both student and regular bookings)
            $bookedQuery = $db->table('booking_equipment be')
                ->select('SUM(be.quantity) as booked_quantity')
                ->join('bookings b', 'b.id = be.booking_id', 'inner')
                ->where('be.equipment_id', $item['id'])
                ->where('b.event_date', $eventDate)
                ->whereIn('b.status', ['pending', 'confirmed', 'approved'])
                ->get();

            $bookedResult = $bookedQuery->getRowArray();
            $bookedQuantity = (int)($bookedResult['booked_quantity'] ?? 0);

            // Calculate available quantity
            $availableQuantity = $totalQuantity - $bookedQuantity;
            $availableQuantity = max(0, $availableQuantity); // Don't go below 0

            $equipmentAvailability[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'total_quantity' => $totalQuantity,
                'booked_quantity' => $bookedQuantity,
                'available_quantity' => $availableQuantity,
                'rate' => $item['rate'],
                'unit' => $item['unit'],
                'is_available' => $availableQuantity > 0
            ];
        }

        log_message('info', "Equipment availability retrieved for date: {$eventDate}");

        return $this->response->setJSON([
            'success' => true,
            'event_date' => $eventDate,
            'equipment' => $equipmentAvailability
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Get equipment availability error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to fetch equipment availability'
        ])->setStatusCode(500);
    }
}

/**
 * Validate equipment availability before booking
 * Checks if selected equipment quantities are available on the specified date
 */
public function validateEquipmentAvailability()
{
    try {
        $userData = $this->verifyUserAccess();
        
        if (!$userData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized access'
            ])->setStatusCode(401);
        }

        $request = $this->request->getJSON(true);
        $eventDate = $request['event_date'] ?? null;
        $selectedEquipment = $request['selected_equipment'] ?? [];

        if (!$eventDate) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Event date is required'
            ])->setStatusCode(400);
        }

        if (empty($selectedEquipment)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'No equipment selected',
                'all_available' => true
            ]);
        }

        $db = \Config\Database::connect();
        $unavailableItems = [];

        foreach ($selectedEquipment as $equipmentId => $requestedQuantity) {
            if ($requestedQuantity <= 0) {
                continue;
            }

            // Get quantity booked on this date for this equipment
            $bookedQuery = $db->table('booking_equipment be')
                ->select('SUM(be.quantity) as booked_quantity')
                ->join('bookings b', 'b.id = be.booking_id', 'inner')
                ->where('be.equipment_id', $equipmentId)
                ->where('b.event_date', $eventDate)
                ->whereIn('b.status', ['pending', 'confirmed', 'approved'])
                ->get();

            $bookedResult = $bookedQuery->getRowArray();
            $bookedQuantity = (int)($bookedResult['booked_quantity'] ?? 0);

            // Get equipment details
            $equipmentModel = new EquipmentModel();
            $equipment = $equipmentModel->find($equipmentId);

            if (!$equipment) {
                $unavailableItems[] = [
                    'equipment_id' => $equipmentId,
                    'reason' => 'Equipment not found'
                ];
                continue;
            }

            $totalQuantity = $equipment['quantity'];
            $availableQuantity = $totalQuantity - $bookedQuantity;

            if ($requestedQuantity > $availableQuantity) {
                $unavailableItems[] = [
                    'equipment_id' => $equipmentId,
                    'equipment_name' => $equipment['name'],
                    'requested' => $requestedQuantity,
                    'available' => max(0, $availableQuantity),
                    'booked' => $bookedQuantity,
                    'total' => $totalQuantity
                ];
            }
        }

        if (!empty($unavailableItems)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Some equipment quantities are not available on this date',
                'all_available' => false,
                'unavailable_items' => $unavailableItems
            ])->setStatusCode(409);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'All equipment is available',
            'all_available' => true
        ]);

    } catch (\Exception $e) {
        log_message('error', 'Validate equipment availability error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to validate equipment availability'
        ])->setStatusCode(500);
    }
}
}
