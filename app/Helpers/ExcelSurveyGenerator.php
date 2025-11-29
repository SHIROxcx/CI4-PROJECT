<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelSurveyGenerator
{
    const TEMPLATE_PATH = FCPATH . 'assets/templates/faculty_evaluation_template.xlsx';

    /**
     * Generate evaluation form by loading template and populating with X marks only
     * This preserves the entire template design and only adds X marks where needed
     */
    public static function generateEvaluationForm($booking, $surveyData, $filename)
    {
        try {
            if (!file_exists(self::TEMPLATE_PATH)) {
                throw new \Exception('Template file not found: ' . self::TEMPLATE_PATH);
            }

            $spreadsheet = IOFactory::load(self::TEMPLATE_PATH);
            $sheet = $spreadsheet->getActiveSheet();

            // Row 16: Punctuality rating - put X in appropriate column (D=Excellent, E=Very Good, F=Good, G=Fair, H=Poor, I=N/A)
            $rating = $surveyData['staff_punctuality'] ?? null;
            self::placeRatingX($sheet, 16, $rating);

            // Row 18: Property Staff courtesy
            $rating = $surveyData['staff_courtesy_property'] ?? null;
            self::placeRatingX($sheet, 18, $rating);

            // Row 19: Audio Operator courtesy
            $rating = $surveyData['staff_courtesy_audio'] ?? null;
            self::placeRatingX($sheet, 19, $rating);

            // Row 20: Janitor courtesy
            $rating = $surveyData['staff_courtesy_janitor'] ?? null;
            self::placeRatingX($sheet, 20, $rating);

            // Row 22: Facility met expectations
            $rating = $surveyData['facility_level_expectations'] ?? null;
            self::placeRatingX($sheet, 22, $rating);

            // Facility cleanliness has piped values, split them
            $cleanlinessValues = isset($surveyData['facility_cleanliness']) 
                ? explode('|', $surveyData['facility_cleanliness']) 
                : [];
            
            // Row 24: Function Hall/Gym cleanliness
            if (isset($cleanlinessValues[0])) {
                self::placeRatingX($sheet, 24, trim($cleanlinessValues[0]));
            }

            // Row 25: Restrooms
            if (isset($cleanlinessValues[1])) {
                self::placeRatingX($sheet, 25, trim($cleanlinessValues[1]));
            }

            // Row 26: Reception Area
            if (isset($cleanlinessValues[2])) {
                self::placeRatingX($sheet, 26, trim($cleanlinessValues[2]));
            }

            // Equipment maintenance has piped values
            $equipmentValues = isset($surveyData['facility_maintenance']) 
                ? explode('|', $surveyData['facility_maintenance']) 
                : [];
            
            // Rows 28-38: Equipment items (AC, Lightings, Fans, Tables, Chairs, Chair Cover, Podium, Projector, Sound System, Microphone, Others)
            $startRow = 28;
            foreach ($equipmentValues as $index => $rating) {
                $row = $startRow + $index;
                if ($row <= 38) { // Only 11 equipment items
                    self::placeRatingX($sheet, $row, trim($rating));
                }
            }

            // Row 41: Would rent again
            $rentAgain = $surveyData['overall_satisfaction'] ?? null;
            if ($rentAgain) {
                $values = explode('|', $rentAgain);
                if (isset($values[0])) {
                    $answer = trim($values[0]);
                    if ($answer === 'Yes') {
                        $sheet->setCellValue('G41', 'X');
                    } elseif ($answer === 'No') {
                        $sheet->setCellValue('H41', 'X');
                    }
                }
            }

            // Row 42: Would recommend
            if ($rentAgain) {
                $values = explode('|', $rentAgain);
                if (isset($values[1])) {
                    $answer = trim($values[1]);
                    if ($answer === 'Yes') {
                        $sheet->setCellValue('G42', 'X');
                    } elseif ($answer === 'No') {
                        $sheet->setCellValue('H42', 'X');
                    }
                }
            }

            // Row 43-46: How did you find facility (Website, Brochure, Friend, Others)
            $howFound = $surveyData['venue_accuracy_setup'] ?? null;
            if ($howFound) {
                $howFound = trim($howFound);
                if (stripos($howFound, 'Website') !== false) {
                    $sheet->setCellValue('G43', 'X');
                } elseif (stripos($howFound, 'Brochure') !== false) {
                    $sheet->setCellValue('G44', 'X');
                } elseif (stripos($howFound, 'Friend') !== false) {
                    $sheet->setCellValue('G45', 'X');
                } elseif (stripos($howFound, 'Others') !== false || stripos($howFound, 'Other') !== false) {
                    $sheet->setCellValue('G46', 'X');
                }
            }

            // Row 48: Comments/Suggestions
            $comments = $surveyData['most_enjoyed'] ?? '';
            if ($comments) {
                $sheet->setCellValue('B48', $comments);
            }

            // Respondent info
            // Note: Template doesn't have explicit respondent fields, so we skip these or find appropriate cells

            // Save file
            $writer = new Xlsx($spreadsheet);
            $writer->save($filename);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'ExcelSurveyGenerator Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Place an X in the appropriate column based on rating value
     * Columns: D=Excellent, E=Very Good, F=Good, G=Fair, H=Poor, I=N/A
     */
    private static function placeRatingX($sheet, $row, $ratingValue)
    {
        if (!$ratingValue) {
            return;
        }

        $ratingValue = strtolower(trim($ratingValue));

        $columnMap = [
            'excellent' => 'D',
            'very good' => 'E',
            'good' => 'F',
            'fair' => 'G',
            'poor' => 'H',
            'n/a' => 'I',
        ];

        $column = $columnMap[$ratingValue] ?? null;
        if ($column) {
            $cellAddress = $column . $row;
            $sheet->setCellValue($cellAddress, 'X');
            $sheet->getStyle($cellAddress)->getAlignment()->setHorizontal('center');
            $sheet->getStyle($cellAddress)->getFont()->setBold(true);
        }
    }
}
