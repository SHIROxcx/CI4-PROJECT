<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class FileTemplatesController extends BaseController
{
    protected $templatesPath;

    public function __construct()
    {
        $this->templatesPath = FCPATH . 'assets/templates/';
    }

    /**
     * Display file templates management page
     */
    public function index()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $data = [
            'title' => 'File Templates Management',
            'templates' => $this->getTemplateFiles()
        ];

        return view('admin/file_templates', $data);
    }

    /**
     * Get all template files (AJAX)
     */
    public function getTemplates()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid request']);
        }

        try {
            $templates = $this->getTemplateFiles();

            return $this->response->setJSON([
                'success' => true,
                'data' => $templates
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Template fetch error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to fetch templates'
            ]);
        }
    }

    /**
     * Update/Replace a template file
     */
    public function updateTemplate()
    {
        try {
            $templateName = $this->request->getPost('template_name');
            $file = $this->request->getFile('template_file');

            // Log the request details
            log_message('info', 'Template update request received for: ' . $templateName);

            if (!$templateName) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Template name is required'
                ]);
            }

            if (!$file || !$file->isValid()) {
                $error = $file ? $file->getErrorString() : 'No file uploaded';
                log_message('error', 'File validation failed: ' . $error);
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Please select a valid file: ' . $error
                ]);
            }

            // Validate file extension matches the original
            $originalPath = $this->templatesPath . $templateName;
            if (!file_exists($originalPath)) {
                log_message('error', 'Original template not found: ' . $originalPath);
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Original template file not found'
                ]);
            }

            $originalExt = pathinfo($templateName, PATHINFO_EXTENSION);
            $uploadedExt = $file->getExtension();

            if (strtolower($originalExt) !== strtolower($uploadedExt)) {
                log_message('error', "Extension mismatch: expected .{$originalExt}, got .{$uploadedExt}");
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => "File extension must be .{$originalExt}"
                ]);
            }

            // Validate file size (max 10MB)
            if ($file->getSize() > 10485760) {
                log_message('error', 'File size exceeds limit: ' . $file->getSize() . ' bytes');
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'File size must not exceed 10MB'
                ]);
            }

            // Create backup of the original file
            $backupPath = $this->templatesPath . 'backups/';
            if (!is_dir($backupPath)) {
                mkdir($backupPath, 0755, true);
                log_message('info', 'Created backup directory: ' . $backupPath);
            }

            $backupFile = $backupPath . pathinfo($templateName, PATHINFO_FILENAME) . '_' . date('Ymd_His') . '.' . $originalExt;

            // Create backup using file_get_contents/file_put_contents
            log_message('info', 'Creating backup: ' . $backupFile);
            $fileContents = file_get_contents($originalPath);
            if ($fileContents === false) {
                throw new \Exception('Failed to read original file for backup');
            }

            if (file_put_contents($backupFile, $fileContents) === false) {
                throw new \Exception('Failed to create backup file');
            }
            log_message('info', 'Backup created successfully');

            // Alternative approach: Use rename with temporary file
            // This avoids the need to delete and works better on Windows
            $tempName = pathinfo($templateName, PATHINFO_FILENAME) . '_temp_' . time() . '.' . $originalExt;

            log_message('info', 'Moving uploaded file to temporary location: ' . $tempName);

            // Move uploaded file to temp location first
            if (!$file->move($this->templatesPath, $tempName, true)) {
                throw new \Exception('Failed to move uploaded file to temporary location');
            }

            $tempPath = $this->templatesPath . $tempName;

            // Clear cache
            clearstatcache(true, $originalPath);
            clearstatcache(true, $tempPath);

            // Check if we can write to the original file
            if (!is_writable($originalPath)) {
                log_message('error', 'Original file is not writable: ' . $originalPath);
                // Clean up temp file
                @unlink($tempPath);
                throw new \Exception('Original template file is not writable. It may be open in another program.');
            }

            log_message('info', 'Attempting to replace original file with new upload');

            // Try to replace the file using file operations instead of unlink
            $newContents = file_get_contents($tempPath);
            if ($newContents === false) {
                @unlink($tempPath);
                throw new \Exception('Failed to read uploaded file');
            }

            // Overwrite the original file
            if (file_put_contents($originalPath, $newContents) === false) {
                @unlink($tempPath);
                throw new \Exception('Failed to overwrite original template. The file may be open in another program.');
            }

            // Remove temporary file
            @unlink($tempPath);

            log_message('info', 'Template updated successfully: ' . $templateName);

            // Get updated file info
            clearstatcache(true, $originalPath);
            $updatedTemplate = $this->getFileInfo($templateName);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Template updated successfully',
                'data' => $updatedTemplate
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Template update error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'details' => 'Check if the file is open in Word, Excel, or another program and close it before uploading.'
            ]);
        }
    }

    /**
     * Download a template file
     */
    public function downloadTemplate($filename)
    {
        try {
            $filepath = $this->templatesPath . $filename;

            if (!file_exists($filepath)) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Template file not found'
                ]);
            }

            return $this->response->download($filepath, null)->setFileName($filename);

        } catch (\Exception $e) {
            log_message('error', 'Template download error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to download template'
            ]);
        }
    }

    /**
     * Get all template files with their info
     */
    private function getTemplateFiles()
    {
        $templates = [];

        if (!is_dir($this->templatesPath)) {
            return $templates;
        }

        $files = scandir($this->templatesPath);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'backups') {
                continue;
            }

            $filepath = $this->templatesPath . $file;

            if (is_file($filepath)) {
                $templates[] = $this->getFileInfo($file);
            }
        }

        return $templates;
    }

    /**
     * Get file information
     */
    private function getFileInfo($filename)
    {
        $filepath = $this->templatesPath . $filename;

        return [
            'name' => $filename,
            'display_name' => $this->formatDisplayName($filename),
            'size' => filesize($filepath),
            'size_formatted' => $this->formatFileSize(filesize($filepath)),
            'modified' => filemtime($filepath),
            'modified_formatted' => date('F d, Y g:i A', filemtime($filepath)),
            'extension' => pathinfo($filename, PATHINFO_EXTENSION),
            'type' => $this->getFileType($filename)
        ];
    }

    /**
     * Format display name from filename
     */
    private function formatDisplayName($filename)
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = str_replace('_', ' ', $name);
        $name = ucwords($name);
        return $name;
    }

    /**
     * Format file size
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * Get file type description
     */
    private function getFileType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        return match($extension) {
            'xlsx', 'xls' => 'Excel Spreadsheet',
            'docx', 'doc' => 'Word Document',
            'pdf' => 'PDF Document',
            'pptx', 'ppt' => 'PowerPoint Presentation',
            default => 'Document'
        };
    }
}
