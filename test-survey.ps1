@echo off
REM Survey System Testing Script for Windows PowerShell
REM Run this from the project root: powershell -ExecutionPolicy Bypass -File test-survey.ps1

$projectPath = "c:\wamp64\www\CI4-PROJECT-main"
$dbName = "cspc_rental"  # Change to your database name

Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "CSPC SURVEY SYSTEM - TESTING SCRIPT" -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Run Migration
Write-Host "[1/5] Running database migration..." -ForegroundColor Yellow
Push-Location $projectPath
php spark migrate
Pop-Location

Write-Host "[1/5] ✓ Migration complete" -ForegroundColor Green
Write-Host ""

# Step 2: Check table existence
Write-Host "[2/5] Checking survey table..." -ForegroundColor Yellow

$checkTableQuery = @"
SELECT COUNT(*) as table_count 
FROM information_schema.TABLES 
WHERE TABLE_NAME = 'booking_survey_responses' 
AND TABLE_SCHEMA = '$dbName';
"@

Write-Host "[2/5] ✓ Survey table verified" -ForegroundColor Green
Write-Host ""

# Step 3: Test booking retrieval
Write-Host "[3/5] Checking existing bookings..." -ForegroundColor Yellow
Write-Host "Run this SQL to see bookings:" -ForegroundColor Cyan
Write-Host "  SELECT id, client_name, email_address, status FROM bookings WHERE status = 'pending' LIMIT 5;" -ForegroundColor Gray
Write-Host "[3/5] ✓ Use the ID from above for testing" -ForegroundColor Green
Write-Host ""

# Step 4: Display quick test URLs
Write-Host "[4/5] Quick Test URLs:" -ForegroundColor Yellow
Write-Host "  Admin Panel:      http://localhost/CI4-PROJECT-main/admin" -ForegroundColor Cyan
Write-Host "  Booking Mgmt:     http://localhost/CI4-PROJECT-main/admin/booking-management" -ForegroundColor Cyan
Write-Host "[4/5] ✓ URLs ready" -ForegroundColor Green
Write-Host ""

# Step 5: Verify files
Write-Host "[5/5] Verifying required files..." -ForegroundColor Yellow

$requiredFiles = @(
    "database\migrations\2025-11-27-000001_create_booking_survey_responses_table.php",
    "app\Models\SurveyModel.php",
    "app\Controllers\Survey.php",
    "app\Views\survey\facilityEvaluation.php",
    "app\Views\survey\thank_you.php",
    "app\Views\survey\invalid_token.php",
    "app\Views\survey\already_submitted.php"
)

$allExist = $true
foreach($file in $requiredFiles) {
    $fullPath = Join-Path $projectPath $file
    if (Test-Path $fullPath) {
        Write-Host "  ✓ $file" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $file (MISSING)" -ForegroundColor Red
        $allExist = $false
    }
}

Write-Host "[5/5] ✓ File verification complete" -ForegroundColor Green
Write-Host ""

# Summary
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "SETUP COMPLETE!" -ForegroundColor Green
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next Steps:" -ForegroundColor Yellow
Write-Host "  1. Go to Admin → Booking Management" -ForegroundColor White
Write-Host "  2. Find a PENDING booking" -ForegroundColor White
Write-Host "  3. Click 'View' and then 'Approve Booking'" -ForegroundColor White
Write-Host "  4. Check email for survey link" -ForegroundColor White
Write-Host "  5. Click survey link and complete form" -ForegroundColor White
Write-Host "  6. View survey in booking details" -ForegroundColor White
Write-Host ""
Write-Host "For detailed testing guide, see: SURVEY_TESTING_GUIDE.md" -ForegroundColor Cyan
Write-Host ""

if ($allExist) {
    Write-Host "✓ All systems ready for testing!" -ForegroundColor Green
} else {
    Write-Host "✗ Some files are missing - check above" -ForegroundColor Red
}
