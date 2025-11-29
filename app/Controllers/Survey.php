<?php

namespace App\Controllers;

use App\Models\SurveyModel;
use App\Models\BookingModel;

// Manually load helper since autoloader may not catch it
require_once APPPATH . 'Helpers/ExcelSurveyGenerator.php';
use App\Helpers\ExcelSurveyGenerator;

class Survey extends BaseController
{
    protected $surveyModel;
    protected $bookingModel;

    public function __construct()
    {
        $this->surveyModel = new SurveyModel();
        $this->bookingModel = new BookingModel();
    }

    /**
     * Display survey form using unique token
     */
    public function index($token = null)
    {
        if (!$token) {
            return view('survey/invalid_token');
        }

        // Get survey by token
        $survey = $this->surveyModel->getByToken($token);

        if (!$survey) {
            return view('survey/invalid_token');
        }

        // Get booking details
        $booking = $this->bookingModel->find($survey['booking_id']);

        if (!$booking) {
            return view('survey/invalid_token');
        }

        // If survey already submitted, show thank you
        if ($survey['staff_punctuality'] !== null) {
            return view('survey/already_submitted', [
                'booking' => $booking,
                'survey' => $survey
            ]);
        }

        return view('survey/facilityEvaluation', [
            'booking' => $booking,
            'survey' => $survey,
            'token' => $token
        ]);
    }

    /**
     * Submit survey response
     */
    public function submit()
    {
        try {
            $token = $this->request->getPost('survey_token');
            log_message('info', '=== SURVEY SUBMIT START ===');
            log_message('info', 'Token received: ' . ($token ? substr($token, 0, 20) . '...' : 'NONE'));

            if (!$token) {
                log_message('error', 'No survey token provided');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid survey token'
                ]);
            }

            // Get survey by token
            $survey = $this->surveyModel->getByToken($token);
            log_message('info', 'Survey lookup result: ' . ($survey ? 'Found' : 'Not Found'));

            if (!$survey) {
                log_message('error', 'Survey not found for token: ' . substr($token, 0, 20));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Survey not found'
                ]);
            }

            log_message('info', 'Survey found - Booking ID: ' . $survey['booking_id']);

            // If already submitted, return error
            if ($survey['staff_punctuality'] !== null) {
                log_message('warning', 'Survey already submitted for booking #' . $survey['booking_id']);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'This survey has already been submitted'
                ]);
            }

            // Collect all POST data and filter for database columns
            $allPostData = $this->request->getPost();
            
            log_message('debug', 'All POST data received: ' . json_encode($allPostData));
            
            // Map form fields to database columns
            $fieldMapping = [
                // Staff fields (direct match)
                'staff_punctuality' => 'staff_punctuality',
                'staff_courtesy_property' => 'staff_courtesy_property',
                'staff_courtesy_audio' => 'staff_courtesy_audio',
                'staff_courtesy_janitor' => 'staff_courtesy_janitor',
                // Facility fields (direct match)
                'facility_level_expectations' => 'facility_level_expectations',
                // Equipment/Venue fields - combine multiple form fields into single columns
                'facility_cleanliness_function_hall' => 'facility_cleanliness',
                'facility_cleanliness_classrooms' => 'facility_cleanliness',
                'facility_cleanliness_restrooms' => 'facility_cleanliness',
                'facility_cleanliness_reception' => 'facility_cleanliness',
                'equipment_airconditioning' => 'facility_maintenance',
                'equipment_lighting' => 'facility_maintenance',
                'equipment_electric_fans' => 'facility_maintenance',
                'equipment_tables' => 'facility_maintenance',
                'equipment_monobloc_chairs' => 'facility_maintenance',
                'equipment_chair_cover' => 'facility_maintenance',
                'equipment_podium' => 'facility_maintenance',
                'equipment_multimedia_projector' => 'facility_maintenance',
                'equipment_sound_system' => 'facility_maintenance',
                'equipment_microphone' => 'facility_maintenance',
                'equipment_others' => 'facility_maintenance',
                'overall_would_rent_again' => 'overall_satisfaction',
                'overall_would_recommend' => 'overall_satisfaction',
                'overall_how_found_facility' => 'venue_accuracy_setup',
                'comments_suggestions' => 'most_enjoyed'
            ];
            
            $surveyData = [];
            
            // Map form fields to database columns
            foreach ($fieldMapping as $formField => $dbColumn) {
                if (isset($allPostData[$formField]) && $allPostData[$formField] !== '') {
                    // Store both mapped (for DB) and original (for Excel generator)
                    if (!isset($surveyData[$dbColumn])) {
                        $surveyData[$dbColumn] = $allPostData[$formField];
                    } else {
                        // If already set, append (for multiple values mapping to one column)
                        $surveyData[$dbColumn] .= ' | ' . $allPostData[$formField];
                    }
                    
                    // Also preserve original field names for Excel generator
                    $surveyData[$formField] = $allPostData[$formField];
                }
            }
            
            // Also capture the comments separately
            if (isset($allPostData['comments_suggestions']) && $allPostData['comments_suggestions'] !== '') {
                $surveyData['improvements_needed'] = $allPostData['comments_suggestions'];
            }

            // Add submission flag
            $surveyData['is_submitted'] = 1;

            log_message('debug', 'Mapped survey data: ' . json_encode($surveyData));
            log_message('info', 'Survey data field count: ' . count($surveyData));

            if (count($surveyData) <= 1) { // Only has is_submitted
                log_message('error', 'No valid survey data - only is_submitted flag present');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No survey data submitted'
                ]);
            }

            // Update survey response
            log_message('info', 'About to update survey - Data count: ' . count($surveyData) . ', Data: ' . json_encode($surveyData));
            $updated = $this->surveyModel->updateSurvey($survey['booking_id'], $surveyData);

            if ($updated) {
                log_message('info', "Survey submitted for booking #{$survey['booking_id']} with token: {$token}");
                
                // Generate Excel file with survey responses
                try {
                    $booking = $this->bookingModel->find($survey['booking_id']);
                    
                    // Create unique filename
                    $timestamp = date('YmdHis');
                    $filename = 'CSPC_Evaluation_Booking_' . $survey['booking_id'] . '_' . $timestamp . '.xlsx';
                    $filepath = WRITEPATH . 'uploads/' . $filename;
                    
                    // Ensure uploads directory exists
                    if (!is_dir(WRITEPATH . 'uploads/')) {
                        mkdir(WRITEPATH . 'uploads/', 0755, true);
                    }
                    
                    // Generate the Excel file
                    ExcelSurveyGenerator::generateEvaluationForm($booking, $surveyData, $filepath);
                    
                    log_message('info', 'Excel evaluation form generated: ' . $filepath);
                    
                    // You can optionally save the filename to database for tracking
                    // $this->bookingModel->update($survey['booking_id'], ['survey_file' => $filename]);
                    
                } catch (\Exception $e) {
                    log_message('error', 'Failed to generate Excel file: ' . $e->getMessage());
                    // Don't fail the survey submission if Excel generation fails
                }
                
                log_message('info', '=== SURVEY SUBMIT SUCCESS ===');
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Thank you! Your survey has been submitted successfully',
                    'redirect' => base_url('survey/thank-you')
                ]);
            } else {
                log_message('error', 'Survey update returned false for booking #' . $survey['booking_id']);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to submit survey. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('critical', 'Survey submit exception: ' . $e->getMessage());
            log_message('critical', 'Exception stack: ' . $e->getTraceAsString());
            log_message('info', '=== SURVEY SUBMIT ERROR ===');
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Thank you page
     */
    public function thankYou()
    {
        return view('survey/thank_you');
    }

    /**
     * API: Get survey by booking ID (admin only)
     */
    public function getSurvey($bookingId)
    {
        if (!$this->isAdmin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized'
            ])->setStatusCode(403);
        }

        $survey = $this->surveyModel->getByBookingId($bookingId);

        if (!$survey) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Survey not found'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $survey
        ]);
    }

    /**
     * Check if user is admin
     */
    private function isAdmin()
    {
        $userRole = session('role');
        return $userRole === 'admin' || $userRole === 'super_admin';
    }

    /**
     * API: Get evaluation files for booking
     */
    public function getEvaluationFiles($bookingId)
    {
        $files = [];
        $uploadsDir = WRITEPATH . 'uploads/';
        
        // Look for Excel evaluation files matching the booking ID
        if (is_dir($uploadsDir)) {
            $pattern = 'CSPC_Evaluation_Booking_' . $bookingId . '_*.xlsx';
            $matchedFiles = glob($uploadsDir . $pattern);
            
            if ($matchedFiles) {
                foreach ($matchedFiles as $filepath) {
                    $filename = basename($filepath);
                    $files[] = [
                        'name' => $filename,
                        'path' => $filename,
                        'size' => filesize($filepath),
                        'created' => filemtime($filepath),
                        'url' => base_url('api/bookings/evaluation-file/' . urlencode($filename))
                    ];
                }
                
                // Sort by creation date (newest first)
                usort($files, function($a, $b) {
                    return $b['created'] - $a['created'];
                });
            }
        }
        
        return $this->response->setJSON([
            'success' => true,
            'files' => $files,
            'count' => count($files)
        ]);
    }

    /**
     * Download evaluation file
     */
    public function downloadEvaluationFile($filename)
    {
        $filename = basename($filename); // Prevent directory traversal
        $filepath = WRITEPATH . 'uploads/' . $filename;

        log_message('info', '=== EVALUATION FILE DOWNLOAD STARTED ===');
        log_message('info', 'Requested filename: ' . $filename);
        log_message('info', 'Full filepath: ' . $filepath);
        log_message('info', 'WRITEPATH: ' . WRITEPATH);
        log_message('info', 'Base URL: ' . base_url());
        log_message('info', 'Current URL: ' . current_url());

        // Check if uploads directory exists
        $uploadsDir = WRITEPATH . 'uploads/';
        if (!is_dir($uploadsDir)) {
            log_message('error', 'Uploads directory does not exist: ' . $uploadsDir);
            log_message('error', 'WRITEPATH value: ' . WRITEPATH);
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Uploads directory not found',
                'writepath' => WRITEPATH,
                'expected_dir' => $uploadsDir
            ]);
        }
        log_message('info', 'Uploads directory exists and is readable');

        // Verify file exists and matches pattern
        if (!file_exists($filepath)) {
            log_message('error', 'Evaluation file not found at path: ' . $filepath);

            // List files in directory for debugging
            $files = scandir($uploadsDir);
            log_message('info', 'Files in uploads directory: ' . json_encode($files));
            log_message('info', 'Number of files found: ' . count($files));
            
            // Look for similar files
            $pattern = 'CSPC_Evaluation_*';
            $similarFiles = glob($uploadsDir . $pattern);
            log_message('info', 'Similar files matching pattern "' . $pattern . '": ' . json_encode($similarFiles));

            return $this->response->setStatusCode(404)->setJSON([
                'error' => 'File not found',
                'filepath' => $filepath,
                'requested_filename' => $filename,
                'directory_contents' => $files,
                'similar_files' => $similarFiles
            ]);
        }

        log_message('info', 'File exists at: ' . $filepath);

        // Get file info
        $fileSize = filesize($filepath);
        $isReadable = is_readable($filepath);
        $filePerms = substr(sprintf('%o', fileperms($filepath)), -4);
        log_message('info', 'File size: ' . $fileSize . ' bytes');
        log_message('info', 'File readable: ' . ($isReadable ? 'YES' : 'NO'));
        log_message('info', 'File permissions: ' . $filePerms);

        if (!$isReadable) {
            log_message('error', 'File is not readable: ' . $filepath);
            log_message('error', 'File permissions: ' . $filePerms);
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'File is not readable',
                'file_permissions' => $filePerms
            ]);
        }

        // Verify it's an evaluation file
        if (strpos($filename, 'CSPC_Evaluation_Booking_') !== 0) {
            log_message('error', 'Invalid evaluation file requested: ' . $filename);
            log_message('warning', 'File does not start with CSPC_Evaluation_Booking_');
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'Access denied - Invalid file pattern',
                'filename' => $filename
            ]);
        }

        log_message('info', 'File validation passed - file matches expected pattern');

        try {
            // Check file extension
            $extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
            log_message('info', 'File extension: ' . $extension);
            
            if ($extension !== 'xlsx' && $extension !== 'xls') {
                log_message('warning', 'Unusual file extension detected: ' . $extension);
            }

            // Detect MIME type
            $mime = mime_content_type($filepath);
            if (!$mime) {
                $mime = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                log_message('info', 'mime_content_type() returned false, using default MIME type: ' . $mime);
            } else {
                log_message('info', 'Detected MIME type: ' . $mime);
            }

            log_message('info', 'Reading file content...');

            // Read file content
            $fileContent = file_get_contents($filepath);
            if ($fileContent === false) {
                log_message('error', 'Failed to read file content using file_get_contents(): ' . $filepath);
                return $this->response->setStatusCode(500)->setJSON([
                    'error' => 'Failed to read file',
                    'filepath' => $filepath
                ]);
            }

            $contentLength = strlen($fileContent);
            log_message('info', 'File content read successfully. Content length: ' . $contentLength . ' bytes');

            if ($contentLength === 0) {
                log_message('error', 'File content is empty!');
                return $this->response->setStatusCode(500)->setJSON([
                    'error' => 'File is empty',
                    'filepath' => $filepath,
                    'actual_file_size' => $fileSize
                ]);
            }

            if ($contentLength !== $fileSize) {
                log_message('warning', 'Content length mismatch - Expected: ' . $fileSize . ', Got: ' . $contentLength);
            }

            log_message('info', 'Setting response headers...');
            log_message('info', 'Content-Type: ' . $mime);
            log_message('info', 'Content-Disposition: attachment; filename="' . $filename . '"');
            log_message('info', 'Content-Length: ' . $contentLength);

            $response = $this->response
                ->setHeader('Content-Type', $mime)
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->setHeader('Pragma', 'no-cache')
                ->setHeader('Expires', '0')
                ->setBody($fileContent);

            log_message('info', 'Response prepared successfully');
            log_message('info', '=== EVALUATION FILE DOWNLOAD COMPLETED SUCCESSFULLY ===');

            return $response;
        } catch (\Exception $e) {
            log_message('error', 'Exception during download: ' . $e->getMessage());
            log_message('error', 'Exception code: ' . $e->getCode());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Download failed: ' . $e->getMessage(),
                'exception_code' => $e->getCode()
            ]);
        }
    }
}
