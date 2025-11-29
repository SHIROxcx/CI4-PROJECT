<?php

namespace App\Models;

use CodeIgniter\Model;

class ExtensionFileModel extends Model
{
    protected $table = 'extension_files';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'extension_id',
        'file_type',
        'original_filename',
        'stored_filename',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
        'upload_date',
        'status',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Upload a file for extension
     */
    public function uploadFile($extensionId, $fileType, $originalFilename, $storedFilename, $filePath, $fileSize, $mimeType, $uploadedById)
    {
        try {
            // Check if extension exists
            $extensionModel = new BookingExtensionModel();
            $extension = $extensionModel->find($extensionId);

            if (!$extension) {
                throw new \Exception('Extension not found');
            }

            $fileData = [
                'extension_id' => $extensionId,
                'file_type' => $fileType,
                'original_filename' => $originalFilename,
                'stored_filename' => $storedFilename,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'uploaded_by' => $uploadedById,
                'upload_date' => date('Y-m-d H:i:s'),
                'status' => 'active',
            ];

            if ($this->insert($fileData)) {
                return [
                    'success' => true,
                    'file_id' => $this->getInsertID(),
                    'message' => 'File uploaded successfully',
                ];
            }

            throw new \Exception('Failed to save file record');
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get files for an extension
     */
    public function getExtensionFiles($extensionId, $fileType = null)
    {
        $query = $this->where('extension_id', $extensionId)
            ->where('status', 'active');

        if ($fileType) {
            $query->where('file_type', $fileType);
        }

        return $query->orderBy('upload_date', 'DESC')->findAll();
    }

    /**
     * Delete a file (soft delete)
     */
    public function deleteFile($fileId)
    {
        try {
            $file = $this->find($fileId);

            if (!$file) {
                throw new \Exception('File not found');
            }

            // Soft delete - mark as deleted
            $this->update($fileId, ['status' => 'deleted']);

            // Optionally delete physical file
            if (file_exists($file['file_path'])) {
                unlink($file['file_path']);
            }

            return [
                'success' => true,
                'message' => 'File deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get payment order file for extension
     */
    public function getPaymentOrderFile($extensionId)
    {
        return $this->where('extension_id', $extensionId)
            ->where('file_type', 'payment_order')
            ->where('status', 'active')
            ->first();
    }

    /**
     * Get payment receipt files for extension
     */
    public function getPaymentReceiptFiles($extensionId)
    {
        return $this->where('extension_id', $extensionId)
            ->where('file_type', 'payment_receipt')
            ->where('status', 'active')
            ->findAll();
    }

    /**
     * Check if payment receipt exists
     */
    public function hasPaymentReceipt($extensionId)
    {
        return $this->where('extension_id', $extensionId)
            ->where('file_type', 'payment_receipt')
            ->where('status', 'active')
            ->countAllResults() > 0;
    }

    /**
     * Count files by type for extension
     */
    public function countFilesByType($extensionId, $fileType)
    {
        return $this->where('extension_id', $extensionId)
            ->where('file_type', $fileType)
            ->where('status', 'active')
            ->countAllResults();
    }
}
