## File Templates - Auto-Populate Signatory System

### How It Works

The system now **automatically loads and displays saved signatory information** for each template file.

#### 1. **Data Storage Structure**

Each template has its signatories stored in a JSON file:

```
Templates: public/assets/templates/
├── Booking_Form.docx
├── Consent_Letter.docx
└── Agreement.xlsx

Signatories Data: writable/signatories/
├── Booking_Form_signatories.json
├── Consent_Letter_signatories.json
└── Agreement_signatories.json
```

#### 2. **Auto-Population Flow**

When you visit the File Templates page:

1. **Controller loads all templates** (`FileTemplatesController::index()`)
2. **For each template**, it automatically:
   - Checks if a signatory JSON file exists
   - Reads the saved data (if exists)
   - Passes it to the view
3. **View displays the form fields** pre-filled with the saved values
4. **Metadata shown** - Each card displays when it was last updated

#### 3. **Signatory JSON File Format**

Each `_signatories.json` file contains:

```json
{
  "template_name": "Booking_Form.docx",
  "signatory_1": "John Doe",
  "signatory_1_title": "Director",
  "signatory_2": "Jane Smith",
  "signatory_2_title": "Manager",
  "signatory_3": "Bob Wilson",
  "signatory_3_title": "Coordinator",
  "additional_info": "Signature required from all three parties",
  "saved_at": "2025-12-17 10:30:45",
  "saved_by": "Admin User"
}
```

#### 4. **Files Modified/Created**

**Modified:**

- `app/Controllers/Admin/FileTemplatesController.php`

  - Added `getSignatoriesForTemplate()` - Retrieves saved data
  - Added `saveSignatoriesToFile()` - Saves data to JSON
  - Added `getSignatories()` - AJAX endpoint for individual template data
  - Updated `index()` - Auto-loads signatory data for all templates

- `app/Views/admin/file_templates.php`

  - Form fields now have `value` attributes populated from `$template['signatories']`
  - Shows last update timestamp on each card header

- `app/Config/Routes.php`
  - Added route: `POST admin/file-templates/get-signatories`

**Created:**

- `app/Config/TemplateSignatories.php`
  - Configuration file documenting the system and field structure

#### 5. **How to Use**

1. Go to File Templates page
2. **Forms auto-populate** with previously saved signatory data
3. Edit any fields you need to change
4. Click **"Save All Changes"**
5. System saves to JSON and shows update timestamp

#### 6. **Key Features**

✅ **Auto-Population** - All saved data loads automatically  
✅ **Real-time Updates** - Shows "Updated: [timestamp]" on each card  
✅ **Persistent Storage** - Data stored in writable/signatories/ directory  
✅ **Per-Template Tracking** - Each template has its own signatory data  
✅ **Audit Trail** - Tracks who saved and when  
✅ **Easy Retrieval** - Can fetch individual template data via AJAX

#### 7. **API Endpoints**

```
POST /admin/file-templates/save-signatories
  - Saves signatory data for multiple templates

POST /admin/file-templates/get-signatories
  - Retrieves saved data for a specific template
  - Requires: template_name parameter
```

### Example Usage

**First Load:**

- Template card shows empty form fields
- No update timestamp (data not yet saved)

**After Saving:**

- Template card shows all filled-in signatory names and titles
- Shows "✓ Updated: 2025-12-17 10:30:45" in header
- Next page load: forms automatically populated with saved values

---

**Status:** ✅ Implementation Complete - Auto-population fully functional!
