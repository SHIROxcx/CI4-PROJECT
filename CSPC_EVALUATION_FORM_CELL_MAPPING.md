# CSPC Facility Evaluation Form - Cell Mapping Reference

## 📊 Form Structure Overview

Based on the provided facility evaluation form image, here's the complete cell mapping:

---

## Header Section

| Cell  | Content                                  | Notes                         |
| ----- | ---------------------------------------- | ----------------------------- |
| A1:I1 | **CSPC RENTAL FACILITY EVALUATION FORM** | Title (merged, bold, size 14) |
| A3    | FACILITY RENTED:                         | Label                         |
| B3    | [Facility Name]                          | From booking data             |
| D3    | DATE:                                    | Label                         |
| D4    | [Booking Date]                           | From booking created_at       |
| A4    | EVENT:                                   | Label                         |
| B4    | [Event Name]                             | From booking event_name       |

---

## Rating Headers (Row 6)

| Column | Header                                                                             | Width |
| ------ | ---------------------------------------------------------------------------------- | ----- |
| A      | No.                                                                                | 5     |
| B      | Please rate the following and check the answer that best describes your experience | 50    |
| C      | [Spacer]                                                                           | 3     |
| D      | **Excellent**                                                                      | 12    |
| E      | **Very Good**                                                                      | 12    |
| F      | **Good**                                                                           | 12    |
| G      | **Fair**                                                                           | 12    |
| H      | **Poor**                                                                           | 12    |
| I      | **N/A**                                                                            | 8     |

---

## STAFF Section (Rows 7-11)

### Row 7: Section Header

| Cell | Content                           |
| ---- | --------------------------------- |
| A7   | **STAFF** (bold, blue background) |

### Row 8: Staff Functionality

| Cell  | Content                                                        | Field               |
| ----- | -------------------------------------------------------------- | ------------------- |
| A8    | 1.                                                             |
| B8    | Functionality of the staff (Property Staff and Audio Operator) |
| D8-I8 | Rating X marks                                                 | `staff_punctuality` |

### Row 9: Staff Courtesy (Sub-header)

| Cell | Content                                                       |
| ---- | ------------------------------------------------------------- |
| A9   | 2.                                                            |
| B9   | Level of courtesy, respect, and helpfulness of the following: |

### Row 10: Property Staff

| Cell    | Content           | Field                     |
| ------- | ----------------- | ------------------------- |
| B10     | a. Property Staff |
| D10-I10 | Rating X marks    | `staff_courtesy_property` |

### Row 11: Audio Operator

| Cell    | Content           | Field                  |
| ------- | ----------------- | ---------------------- |
| B11     | b. Audio Operator |
| D11-I11 | Rating X marks    | `staff_courtesy_audio` |

### Row 12: Janitor

| Cell    | Content        | Field                    |
| ------- | -------------- | ------------------------ |
| B12     | c. Janitor     |
| D12-I12 | Rating X marks | `staff_courtesy_janitor` |

---

## FACILITY Section (Rows 13-27)

### Row 13: Section Header

| Cell | Content                              |
| ---- | ------------------------------------ |
| A13  | **FACILITY** (bold, blue background) |

### Row 14: Facility Expectations

| Cell    | Content                                           | Field                         |
| ------- | ------------------------------------------------- | ----------------------------- |
| A14     | 1.                                                |
| B14     | Level at which the facility met your expectations |
| D14-I14 | Rating X marks                                    | `facility_level_expectations` |

### Row 15: Cleanliness (Sub-header)

| Cell | Content                           |
| ---- | --------------------------------- |
| A15  | 2.                                |
| B15  | The cleanliness of the following: |

### Rows 16-19: Cleanliness Items

| Row | Item                                                     | Field                  |
| --- | -------------------------------------------------------- | ---------------------- |
| 16  | a. Function Hall / Gym / Auditorium / Seminar Hall / ATM | `facility_cleanliness` |
| 17  | b. Classrooms & Rooms                                    | `facility_cleanliness` |
| 18  | c. Restrooms                                             | `facility_cleanliness` |
| 19  | d. Reception Area                                        | `facility_cleanliness` |

### Row 20: Equipment (Sub-header)

| Cell | Content                                              |
| ---- | ---------------------------------------------------- |
| A20  | 3.                                                   |
| B20  | Please rate the function of the following equipment: |

### Rows 21-31: Equipment Items

| Row | Item                    | Field                  |
| --- | ----------------------- | ---------------------- |
| 21  | a. Airconditioning unit | `facility_maintenance` |
| 22  | b. Lightings            | `facility_maintenance` |
| 23  | c. Electric Fans        | `facility_maintenance` |
| 24  | d. Tables               | `facility_maintenance` |
| 25  | e. Monobloc Chairs      | `facility_maintenance` |
| 26  | f. Chair Cover          | `facility_maintenance` |
| 27  | g. Podium               | `facility_maintenance` |
| 28  | h. Multimedia Projector | `facility_maintenance` |
| 29  | i. Sound System         | `facility_maintenance` |
| 30  | j. Microphone           | `facility_maintenance` |
| 31  | k. Others               | `facility_maintenance` |

---

## OVERALL EXPERIENCE Section (Rows 32-39)

### Row 32: Section Header

| Cell | Content                                        |
| ---- | ---------------------------------------------- |
| A32  | **OVERALL EXPERIENCE** (bold, blue background) |

### Row 33: Would Rent Again

| Cell | Content                             | Field                      |
| ---- | ----------------------------------- | -------------------------- |
| A33  | 1.                                  |
| B33  | Would you rent this facility again? |
| D33  | Yes (X if applicable)               | `overall_would_rent_again` |
| H33  | No (X if applicable)                | `overall_would_rent_again` |

### Row 34: Would Recommend

| Cell | Content                                      | Field                     |
| ---- | -------------------------------------------- | ------------------------- |
| A34  | 2.                                           |
| B34  | Would you recommend this facility to others? |
| D34  | Yes (X if applicable)                        | `overall_would_recommend` |
| H34  | No (X if applicable)                         | `overall_would_recommend` |

### Row 35: How Did You Find

| Cell | Content                               |
| ---- | ------------------------------------- |
| A35  | 3.                                    |
| B35  | How did you find about this facility? |

### Rows 36-39: Finding Options

| Row | Option   | Field                        |
| --- | -------- | ---------------------------- |
| 36  | Website  | `overall_how_found_facility` |
| 37  | Brochure | `overall_how_found_facility` |
| 38  | Friend   | `overall_how_found_facility` |
| 39  | Others   | `overall_how_found_facility` |

---

## COMMENTS/SUGGESTIONS Section (Rows 40-42)

### Row 40: Section Header

| Cell | Content                          |
| ---- | -------------------------------- |
| A40  | **COMMENTS/SUGGESTIONS:** (bold) |

### Row 41-43: Comment Box

| Cell    | Content                           | Field                  |
| ------- | --------------------------------- | ---------------------- |
| B41:I43 | [Comments text] (merged, wrapped) | `comments_suggestions` |

---

## Database Field Mapping

```php
[
    // Staff fields
    'staff_punctuality' => 'Rating value (Excellent, Very Good, etc.)',
    'staff_courtesy_property' => 'Rating value',
    'staff_courtesy_audio' => 'Rating value',
    'staff_courtesy_janitor' => 'Rating value',

    // Facility fields
    'facility_level_expectations' => 'Rating value',
    'facility_cleanliness' => 'Rating value (applies to all cleanliness items)',
    'facility_maintenance' => 'Rating value (applies to all equipment items)',

    // Overall fields
    'overall_would_rent_again' => 'Yes or No',
    'overall_would_recommend' => 'Yes or No',
    'overall_how_found_facility' => 'Website, Brochure, Friend, Others',

    // Comments
    'comments_suggestions' => 'Free text'
]
```

---

## Styling Notes

### Headers (Section Names)

- **Font**: Bold, 11pt
- **Background Color**: Light blue (#D9E1F2)
- **Text Color**: Black
- **Alignment**: Left
- **Border**: All borders

### Rating Headers (Row 6)

- **Font**: Bold, 10pt
- **Background Color**: Light blue (#D9E1F2)
- **Alignment**: Center
- **Border**: All borders

### Rating Cells (D-I columns)

- **Alignment**: Center
- **Border**: All borders
- **Format**: X mark for selected rating

### Content Cells

- **Font**: Normal, 10pt
- **Alignment**: Left (except ratings = Center)
- **Border**: All borders
- **Wrap Text**: Enabled for descriptions

---

## Color Scheme

- **Section Headers**: Light Blue (#D9E1F2)
- **Rating Headers**: Light Blue (#D9E1F2)
- **Text Color**: Black (#000000)
- **Borders**: Black lines (1pt)

---

## Page 2 Notes

The form appears to continue on page 2 as shown in the image. The structure remains the same with:

- Same column headers
- Same rating system (Excellent, Very Good, Good, Fair, Poor, N/A)
- All sections maintained

---

**Last Updated**: November 28, 2025  
**Version**: 1.0 - Complete Cell Mapping Reference
