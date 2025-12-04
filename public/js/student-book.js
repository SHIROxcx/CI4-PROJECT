// ========================================
// STUDENT BOOKING - FOR DASHBOARD
// Auto-fills user data from session
// ========================================

let selectedStudentFacility = null;
let selectedStudentFacilityId = null;
let selectedStudentPlanId = null;
let selectedStudentEquipment = {};
let uploadedStudentFiles = {
  permission: null,
  request: null,
  approval: null,
};

// ========================================
// OPEN BOOKING MODAL
// ========================================
function openStudentBookingModal(facilityKey, facilityId) {
  selectedStudentFacility = facilityKey;
  selectedStudentFacilityId = facilityId;

  // Check facility status first
  checkStudentFacilityStatus(facilityKey).then((status) => {
    loadStudentFacilityData(facilityKey, status);
  });

  document.getElementById("studentBookingModal").style.display = "block";
}

function closeStudentModal() {
  document.getElementById("studentBookingModal").style.display = "none";
  resetStudentForm();
}

// Check facility status and return status object
async function checkStudentFacilityStatus(facilityKey) {
  try {
    const response = await fetch(`/api/facilities/list`);
    const data = await response.json();

    if (data.success && data.facilities) {
      const facility = data.facilities.find(
        (f) => f.facility_key === facilityKey
      );
      if (facility) {
        return {
          is_active: facility.is_active,
          is_maintenance: facility.is_maintenance,
          name: facility.name,
        };
      }
    }
    return { is_active: 1, is_maintenance: 0, name: "Unknown" };
  } catch (error) {
    console.error("Error checking facility status:", error);
    return { is_active: 1, is_maintenance: 0, name: "Unknown" };
  }
}

// ========================================
// LOAD FACILITY DATA
// ========================================
async function loadStudentFacilityData(facilityKey, facilityStatus = {}) {
  try {
    console.log("Loading facility data for:", facilityKey);

    const response = await fetch(`/api/student/facilities/${facilityKey}/data`);

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success && data.facility) {
      document.getElementById(
        "modalTitle"
      ).textContent = `Book ${data.facility.name}`;

      // Check facility availability
      const isInactive = facilityStatus.is_active == 0;
      const isMaintenance = facilityStatus.is_maintenance == 1;

      const modalBody = document
        .getElementById("studentBookingModal")
        .querySelector(".modal-content");
      const statusMessageDiv = modalBody.querySelector(
        ".facility-status-message"
      );

      if (isInactive) {
        // Show inactive message
        if (statusMessageDiv) statusMessageDiv.remove();
        const inactiveMsg = document.createElement("div");
        inactiveMsg.className = "alert alert-danger facility-status-message";
        inactiveMsg.innerHTML =
          '<i class="fas fa-ban"></i> <strong>Sorry!</strong> This facility is not available right now.';
        modalBody.insertBefore(inactiveMsg, modalBody.firstChild);

        // Disable the form
        disableStudentBookingForm();
      } else if (isMaintenance) {
        // Show maintenance message
        if (statusMessageDiv) statusMessageDiv.remove();
        const maintenanceMsg = document.createElement("div");
        maintenanceMsg.className =
          "alert alert-warning facility-status-message";
        maintenanceMsg.innerHTML =
          '<i class="fas fa-wrench"></i> <strong>Under Maintenance</strong> This facility is currently under maintenance. Please check back later.';
        modalBody.insertBefore(maintenanceMsg, modalBody.firstChild);

        // Disable the form
        disableStudentBookingForm();
      } else {
        // Remove any existing status messages
        if (statusMessageDiv) statusMessageDiv.remove();

        if (data.facility.plans && data.facility.plans.length > 0) {
          selectedStudentPlanId = parseInt(data.facility.plans[0].id); // ✅ Convert to integer
        }

        // Don't load equipment yet - wait for user to select event date
        // This is now date-based, so equipment availability depends on the selected date
        loadStudentEquipment(); // Will show message to select date first
        enableSubmitButton();
        enableStudentBookingForm();
      }

      console.log("Facility data loaded successfully");
    } else {
      throw new Error(data.message || "Failed to load facility details");
    }
  } catch (error) {
    console.error("Error loading facility:", error);
    showToast("Failed to load facility details", "error");
    closeStudentModal();
  }
}

// ========================================
// CHECK FACILITY AVAILABILITY FOR BOOKING DATE
// ========================================
async function checkBookingFacilityAvailability(selectedDate) {
  if (!selectedStudentFacilityId) return;

  try {
    // Use the unified checkDateConflict endpoint
    // Note: This is just for informational purposes as the real check happens on submit
    console.log("Event date changed to:", selectedDate);
    // The real conflict check will happen when user tries to submit the booking
  } catch (error) {
    console.error("Error checking facility availability:", error);
    // Don't block the user, let them proceed - we'll catch the conflict on submit
  }
}

// ========================================
// LOAD EQUIPMENT - DATE-BASED AVAILABILITY
// ========================================
async function loadStudentEquipment(eventDate = null) {
  const container = document.getElementById("studentEquipmentGrid");
  if (!container) return;

  // If no event date is selected, show a message
  if (!eventDate) {
    container.innerHTML =
      '<p style="text-align: center; color: var(--gray); padding: 20px;">📅 Please select an event date first to see available equipment for that date.</p>';
    return;
  }

  // Show loading state
  container.innerHTML =
    '<p style="text-align: center; color: var(--gray); padding: 20px;">Loading equipment availability...</p>';

  try {
    const response = await fetch(
      `/api/student/equipment/availability?event_date=${eventDate}`
    );

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();

    if (data.success && data.equipment) {
      displayStudentEquipment(data.equipment, eventDate);
    } else {
      console.warn("No equipment data returned");
      displayStudentEquipment([], eventDate);
    }
  } catch (error) {
    console.error("Error loading equipment:", error);
    showToast("Failed to load equipment list", "warning");
    container.innerHTML =
      '<p style="text-align: center; color: var(--gray);">Unable to load equipment at this time.</p>';
  }
}

function displayStudentEquipment(equipmentList, eventDate) {
  const container = document.getElementById("studentEquipmentGrid");
  if (!container) return;
  container.innerHTML = "";

  // Filter equipment that has availability on the selected date
  const availableEquipment = equipmentList.filter(
    (eq) => eq.available_quantity > 0
  );

  // Group by category
  const grouped = {};
  availableEquipment.forEach((eq) => {
    // Use a default category if none provided
    const category = eq.category || "other";
    if (!grouped[category]) {
      grouped[category] = [];
    }
    grouped[category].push(eq);
  });

  // Display date info
  if (eventDate) {
    const dateHeader = document.createElement("div");
    dateHeader.style.gridColumn = "1 / -1";
    dateHeader.style.marginBottom = "15px";
    dateHeader.style.padding = "10px";
    dateHeader.style.background = "#e3f2fd";
    dateHeader.style.borderRadius = "8px";
    dateHeader.style.textAlign = "center";
    dateHeader.innerHTML = `<strong>📅 Equipment available on ${new Date(
      eventDate
    ).toLocaleDateString()}</strong>`;
    container.appendChild(dateHeader);
  }

  Object.keys(grouped).forEach((category) => {
    const header = document.createElement("h4");
    header.className = "equipment-category-header";
    header.style.gridColumn = "1 / -1";
    header.style.marginTop = "20px";
    header.style.marginBottom = "10px";
    header.style.color = "var(--primary)";
    header.textContent = category.replace("_", " ").toUpperCase();
    container.appendChild(header);

    grouped[category].forEach((equipment) => {
      const equipDiv = document.createElement("div");
      equipDiv.className = "equipment-card";
      equipDiv.innerHTML = `
        <div class="equipment-info">
          <h4 class="equipment-name">${equipment.name}</h4>
          <p class="equipment-description">${
            equipment.available_quantity
          } available on this date</p>
          ${
            equipment.rate > 0
              ? `<span class="equipment-rate">₱${equipment.rate}/${equipment.unit}</span>`
              : '<span class="included-badge">Included</span>'
          }
        </div>
        <div class="equipment-actions-card">
          <input type="number" class="form-control qty-input"
                 id="student-qty-${equipment.id}"
                 min="0" max="${equipment.available_quantity}" value="0"
                 onchange="updateStudentEquipment(${equipment.id})">
          <label class="equipment-label">Quantity</label>
        </div>
      `;
      container.appendChild(equipDiv);
    });
  });

  if (availableEquipment.length === 0) {
    container.innerHTML =
      '<p style="text-align: center; color: var(--gray); padding: 20px;">No equipment available for the selected date.</p>';
  }
}

function updateStudentEquipment(equipmentId) {
  const input = document.getElementById(`student-qty-${equipmentId}`);
  const quantity = parseInt(input.value);
  if (quantity > 0) {
    selectedStudentEquipment[equipmentId] = quantity;
  } else {
    delete selectedStudentEquipment[equipmentId];
  }
}

// ========================================
// FILE UPLOAD HANDLER
// ========================================
function handleStudentFileSelect(input, fileType) {
  const file = input.files[0];
  if (!file) return;

  if (file.size > 10 * 1024 * 1024) {
    showToast("File size must be less than 10MB", "error");
    input.value = "";
    return;
  }

  const allowedTypes = [
    "application/pdf",
    "image/jpeg",
    "image/png",
    "image/jpg",
  ];
  if (!allowedTypes.includes(file.type)) {
    showToast("Only PDF, JPG, and PNG files are allowed", "error");
    input.value = "";
    return;
  }

  uploadedStudentFiles[fileType] = file;
  const uploadCard = document.getElementById(`upload-${fileType}`);
  uploadCard.classList.add("uploaded");
  uploadCard.querySelector(".upload-status").textContent = "Uploaded";
  uploadCard.querySelector(".upload-status").style.color = "var(--success)";
  uploadCard.querySelector(".upload-status").style.background = "#d1fae5";
  document.getElementById(
    `filename-${fileType}`
  ).textContent = `✓ ${file.name}`;
  showToast(`${file.name} uploaded successfully`, "success");
}

// ========================================
// ENABLE SUBMIT BUTTON
// ========================================
function enableSubmitButton() {
  const submitBtn = document.getElementById("submitStudentBtn");
  if (submitBtn) {
    submitBtn.disabled = false;
    submitBtn.style.opacity = "1";
    submitBtn.style.cursor = "pointer";
  }
}

function disableStudentBookingForm() {
  const form = document.getElementById("studentBookingForm");
  if (!form) return;

  const inputs = form.querySelectorAll("input, textarea, select, button");
  inputs.forEach((input) => {
    if (input.id !== "submitStudentBtn") {
      input.disabled = true;
    }
  });

  const submitBtn = document.getElementById("submitStudentBtn");
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.textContent = "Facility Unavailable";
    submitBtn.style.opacity = "0.5";
    submitBtn.style.cursor = "not-allowed";
  }
}

function enableStudentBookingForm() {
  const form = document.getElementById("studentBookingForm");
  if (!form) return;

  const inputs = form.querySelectorAll("input, textarea, select");
  inputs.forEach((input) => {
    input.disabled = false;
  });

  const submitBtn = document.getElementById("submitStudentBtn");
  if (submitBtn) {
    submitBtn.disabled = false;
    submitBtn.textContent = "Submit Booking";
    submitBtn.style.opacity = "1";
    submitBtn.style.cursor = "pointer";
  }
}

// ========================================
// FORM VALIDATION
// ========================================
function validateStudentForm() {
  console.log("=== validateStudentForm START ===");

  const validations = [
    {
      id: "contactNumber",
      label: "Contact Number",
      test: (val) => {
        // Basic phone number validation (Philippine format)
        const phoneRegex = /^(09|\+639)\d{9}$/;
        return val.length >= 10 && /^\d+$/.test(val);
      },
      message: "Please enter a valid contact number (e.g., 09123456789)",
    },
    {
      id: "organization",
      label: "Organization",
      test: (val) => val.length >= 3,
      message: "Organization name must be at least 3 characters",
    },
    {
      id: "eventDate",
      label: "Event Date",
      test: (val) => {
        const eventDate = new Date(val);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return eventDate >= today;
      },
      message: "Event date cannot be in the past",
    },
    {
      id: "eventTime",
      label: "Event Time",
      test: (val) => val.length > 0,
      message: "Please select an event time",
    },
    {
      id: "duration",
      label: "Duration",
      test: (val) => {
        const num = parseInt(val);
        return num >= 1 && num <= 12;
      },
      message: "Duration must be between 1 and 12 hours",
    },
    {
      id: "eventTitle",
      label: "Event Title",
      test: (val) => val.length >= 5,
      message: "Event title must be at least 5 characters",
    },
  ];

  validations.forEach((v) => hideInlineError(v.id));
  let isValid = true;
  let firstErrorField = null;
  let errors = [];

  for (const validation of validations) {
    const field = document.getElementById(validation.id);
    if (!field) {
      console.warn(`Field not found: ${validation.id}`);
      continue;
    }
    const value = field.value.trim();

    if (!value) {
      console.log(`Validation failed - ${validation.id} is empty`);
      showInlineError(validation.id, `${validation.label} is required`);
      errors.push(`${validation.label} is required`);
      if (!firstErrorField) firstErrorField = field;
      isValid = false;
      continue;
    }

    if (!validation.test(value)) {
      console.log(
        `Validation failed - ${validation.id} test failed for value: ${value}`
      );
      showInlineError(validation.id, validation.message);
      errors.push(validation.message);
      if (!firstErrorField) firstErrorField = field;
      isValid = false;
    }
  }

  const address = document.getElementById("address").value.trim();
  if (address && address.length < 10) {
    console.log("Address validation failed");
    showInlineError("address", "Address must be at least 10 characters");
    errors.push("Address must be at least 10 characters");
    if (!firstErrorField) firstErrorField = document.getElementById("address");
    isValid = false;
  }

  const attendees = document.getElementById("attendees").value.trim();
  if (attendees && (isNaN(attendees) || parseInt(attendees) < 1)) {
    console.log("Attendees validation failed");
    showInlineError(
      "attendees",
      "Number of attendees must be a positive number"
    );
    errors.push("Number of attendees must be a positive number");
    if (!firstErrorField)
      firstErrorField = document.getElementById("attendees");
    isValid = false;
  }

  // Files are optional - do not require at least one file to be uploaded

  if (!isValid && firstErrorField) {
    firstErrorField.focus();
    // Show detailed error messages in toast
    if (errors.length === 1) {
      showToast(`❌ ${errors[0]}`, "error");
    } else {
      showToast(`❌ Please fix ${errors.length} errors in the form`, "error");
    }
  }

  console.log("=== validateStudentForm END - isValid:", isValid, "===");
  return isValid;
}

// ========================================
// ENHANCED TOAST NOTIFICATIONS - ROBUST VERSION
// ========================================
function showToast(message, type = "info") {
  // Get or create toast container
  let toastContainer = document.getElementById("toastContainer");

  if (!toastContainer) {
    console.warn("Toast container not found, creating one...");
    toastContainer = document.createElement("div");
    toastContainer.id = "toastContainer";
    toastContainer.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      display: flex;
      flex-direction: column;
      gap: 12px;
      max-width: 400px;
      pointer-events: none;
    `;
    document.body.appendChild(toastContainer);
  }

  const toast = document.createElement("div");
  toast.className = `toast toast-${type}`;

  // Set default styles if CSS classes aren't loaded
  toast.style.cssText = `
    background: white;
    border-radius: 12px;
    padding: 16px 20px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    animation: slideInRight 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-left: 4px solid;
    min-width: 300px;
    pointer-events: auto;
  `;

  // Set border color based on type
  const borderColors = {
    success: "#10b981",
    error: "#dc2626",
    warning: "#f59e0b",
    info: "#3b82f6",
  };
  toast.style.borderLeftColor = borderColors[type] || borderColors.info;

  const icon =
    { success: "✅", error: "❌", warning: "⚠️", info: "ℹ️" }[type] || "ℹ️";

  const closeBtn = document.createElement("button");
  closeBtn.className = "toast-close";
  closeBtn.textContent = "×";
  closeBtn.style.cssText = `
    background: none;
    border: none;
    font-size: 24px;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    flex-shrink: 0;
    transition: all 0.2s;
  `;
  closeBtn.onclick = () => {
    toast.style.animation = "fadeOut 0.3s ease-out forwards";
    setTimeout(() => toast.remove(), 300);
  };

  const messageSpan = document.createElement("span");
  messageSpan.className = "toast-message";
  messageSpan.textContent = message;
  messageSpan.style.cssText = `
    flex: 1;
    font-size: 14px;
    color: #1f2937;
    line-height: 1.5;
    word-break: break-word;
  `;

  const iconSpan = document.createElement("span");
  iconSpan.className = "toast-icon";
  iconSpan.textContent = icon;
  iconSpan.style.cssText = `
    font-size: 24px;
    flex-shrink: 0;
  `;

  toast.appendChild(iconSpan);
  toast.appendChild(messageSpan);
  toast.appendChild(closeBtn);

  toastContainer.appendChild(toast);

  // Auto-remove after 5 seconds
  setTimeout(() => {
    if (toast.parentNode) {
      toast.style.animation = "fadeOut 0.3s ease-out forwards";
      setTimeout(() => {
        if (toast.parentNode) toast.remove();
      }, 300);
    }
  }, 5000);

  // Log for debugging
  console.log(`[TOAST ${type.toUpperCase()}]: ${message}`);
}

// ========================================
// UPDATED SUBMIT BOOKING WITH BETTER ERROR HANDLING
// ========================================
async function submitStudentBooking() {
  console.log("✓ submitStudentBooking function called");

  const btn = document.getElementById("submitStudentBtn");
  if (!btn) {
    console.error("Submit button not found!");
    return;
  }

  console.log("Submit button found, disabling...");
  btn.disabled = true;
  btn.textContent = "Processing...";

  try {
    // Validate form first
    console.log("Starting form validation...");
    if (!validateStudentForm()) {
      console.log("Form validation failed");
      btn.disabled = false;
      btn.textContent = "✓ Submit Booking";
      return;
    }

    console.log("Form validation passed");

    // Show processing toast
    showToast("Validating booking details...", "info");

    // Get user data from hidden fields (from session)
    const clientName =
      document.getElementById("clientName")?.value.trim() || "";
    const clientEmail =
      document.getElementById("clientEmail")?.value.trim() || "";
    const contactNumber =
      document.getElementById("contactNumber")?.value.trim() || "";

    // VALIDATION: Ensure critical fields are present
    if (!clientName) {
      throw new Error(
        "User name is missing. Please refresh the page and try again."
      );
    }
    if (!clientEmail) {
      throw new Error(
        "User email is missing. Please refresh the page and try again."
      );
    }
    if (!contactNumber) {
      throw new Error(
        "Contact number is missing. Please update your profile with a contact number."
      );
    }

    console.log("User data:", { clientName, clientEmail, contactNumber });

    // Get all form data
    const eventDate = document.getElementById("eventDate").value;
    const eventTime = document.getElementById("eventTime").value;
    const duration = parseInt(document.getElementById("duration").value);

    // Validate date and time are selected
    if (!eventDate || !eventTime) {
      throw new Error("Please select both event date and time");
    }

    // CHECK FOR DATE CONFLICTS WITH EXISTING BOOKINGS
    showToast("Checking date availability...", "info");

    const conflictCheck = await fetch("/api/bookings/checkDateConflict", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        facility_id: selectedStudentFacilityId,
        event_date: eventDate,
        event_time: eventTime,
        duration: duration,
      }),
    });

    const conflictResult = await conflictCheck.json();
    console.log("Conflict check result:", conflictResult);

    if (
      conflictResult.hasConflict ||
      conflictResult.hasPendingOrApprovedBooking
    ) {
      showToast(
        "❌ This date and time has a conflict with an existing booking. Please select a different date or time.",
        "error"
      );
      btn.disabled = false;
      btn.textContent = "Submit Booking";
      return;
    }

    const bookingData = {
      facility_id: selectedStudentFacilityId,
      plan_id: selectedStudentPlanId,
      client_name: clientName,
      email_address: clientEmail,
      organization: document.getElementById("organization").value.trim(),
      contact_number: contactNumber,
      address: document.getElementById("address").value.trim() || "",
      event_date: eventDate,
      event_time: eventTime,
      duration: duration,
      attendees: document.getElementById("attendees").value || null,
      event_title: document.getElementById("eventTitle").value.trim(),
      special_requirements:
        document.getElementById("specialRequirements").value.trim() || "",
      selected_equipment: selectedStudentEquipment,
    };

    console.log("Sending booking data:", bookingData);

    const bookingResponse = await fetch("/api/student/bookings/create", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify(bookingData),
    });

    console.log("Response status:", bookingResponse.status);

    const responseText = await bookingResponse.text();
    console.log("Response text:", responseText);

    let bookingResult;
    try {
      bookingResult = JSON.parse(responseText);
    } catch (parseError) {
      console.error("JSON Parse Error:", parseError);
      console.error("Response was:", responseText.substring(0, 500));
      throw new Error(
        "Server returned invalid response. Check console for details."
      );
    }

    console.log("Booking result:", bookingResult);

    if (!bookingResult.success) {
      // Check for specific error types
      const message = bookingResult.message || "Failed to create booking";
      const statusCode = bookingResponse.status;

      // Check for date/time availability errors (HTTP 409 Conflict)
      if (
        statusCode === 409 ||
        message.toLowerCase().includes("not available") ||
        message.toLowerCase().includes("unavailable") ||
        message.toLowerCase().includes("facility is not available")
      ) {
        throw new Error(
          `Facility is not available at the selected date and time`
        );
      }

      // Check for equipment availability errors
      if (
        message.toLowerCase().includes("insufficient") ||
        message.toLowerCase().includes("equipment")
      ) {
        throw new Error(`${message}`);
      }

      // Default error message
      throw new Error(message);
    }

    const bookingId = bookingResult.booking_id;

    const hasFiles =
      uploadedStudentFiles.permission ||
      uploadedStudentFiles.request ||
      uploadedStudentFiles.approval;

    console.log("Checking for files to upload...");
    console.log("uploadedStudentFiles:", uploadedStudentFiles);
    console.log("hasFiles:", hasFiles);

    if (hasFiles) {
      try {
        const formData = new FormData();

        console.log("Adding files to FormData...");
        if (uploadedStudentFiles.permission) {
          formData.append("files[]", uploadedStudentFiles.permission);
          console.log(
            "Added permission file:",
            uploadedStudentFiles.permission.name
          );
        }
        if (uploadedStudentFiles.request) {
          formData.append("files[]", uploadedStudentFiles.request);
          console.log("Added request file:", uploadedStudentFiles.request.name);
        }
        if (uploadedStudentFiles.approval) {
          formData.append("files[]", uploadedStudentFiles.approval);
          console.log(
            "Added approval file:",
            uploadedStudentFiles.approval.name
          );
        }

        showToast("Uploading documents...", "info");

        const uploadResponse = await fetch(
          `/api/student/bookings/${bookingId}/upload`,
          {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest" },
            body: formData,
          }
        );

        console.log("Upload response status:", uploadResponse.status);
        const uploadResult = await uploadResponse.json();
        console.log("Upload result:", uploadResult);

        if (!uploadResult.success) {
          console.warn("File upload warning:", uploadResult.message);
          showToast(
            "Booking created but some files failed to upload: " +
              uploadResult.message,
            "warning"
          );
        } else {
          console.log("Files uploaded successfully:", uploadResult.files);
          showToast(
            `${uploadResult.files.length} file(s) uploaded successfully`,
            "success"
          );
        }
      } catch (uploadError) {
        console.error("File upload error:", uploadError);
        console.error("Upload error details:", uploadError.message);
        showToast(
          "Booking created but file upload failed: " + uploadError.message,
          "warning"
        );
      }
    } else {
      console.log("No files to upload - proceeding with booking only");
    }

    showStudentSuccess(bookingId);
    showToast("Booking submitted successfully!", "success");
  } catch (error) {
    console.error("Booking error:", error);
    console.error("Error stack:", error.stack);

    // Format error message for toast
    let errorMessage = error.message || "Failed to submit booking";

    // Always display error as toast
    showToast(errorMessage, "error");

    // Re-enable button
    const submitBtn = document.getElementById("submitStudentBtn");
    if (submitBtn) {
      submitBtn.disabled = false;
      submitBtn.textContent = "✓ Submit Booking";
    }
  }
}
// ========================================
// SUCCESS MESSAGE
// ========================================
function showStudentSuccess(bookingId) {
  const modalBody = document.querySelector("#studentBookingModal .modal-body");
  modalBody.innerHTML = `
    <div class="success-message" style="text-align: center; padding: 40px 20px;">
      <div style="font-size: 64px; margin-bottom: 20px;">✅</div>
      <h3 style="color: var(--success); margin-bottom: 15px;">Booking Submitted Successfully!</h3>
      <p style="margin-bottom: 10px; font-size: 18px;"><strong>Booking ID:</strong> #BK${String(
        bookingId
      ).padStart(3, "0")}</p>
      <p style="margin-bottom: 10px; color: var(--gray);">Your booking request has been submitted for approval.</p>
      <p style="margin-bottom: 30px; color: var(--gray);">You will receive a notification once it has been reviewed.</p>
    </div>
  `;
  document.querySelector("#studentBookingModal .modal-footer").style.display =
    "none";
}

function closeAndReload() {
  closeStudentModal();
  location.reload();
}

// ========================================
// RESET FORM
// ========================================
function resetStudentForm() {
  selectedStudentFacility = null;
  selectedStudentFacilityId = null;
  selectedStudentPlanId = null;
  selectedStudentEquipment = {};
  uploadedStudentFiles = { permission: null, request: null, approval: null };

  const form = document.getElementById("studentBookingForm");
  if (form) form.reset();

  const uploadCards = document.querySelectorAll(".document-upload-card");
  uploadCards.forEach((card) => {
    card.classList.remove("uploaded");
    const statusEl = card.querySelector(".upload-status");
    if (statusEl) {
      statusEl.textContent = "Not uploaded";
      statusEl.style.color = "";
      statusEl.style.background = "";
    }
  });

  const fileNameDisplays = document.querySelectorAll(".file-name-display");
  fileNameDisplays.forEach((el) => (el.textContent = ""));

  const fileInputs = document.querySelectorAll('[id^="file-"]');
  fileInputs.forEach((input) => (input.value = ""));

  const equipmentInputs = document.querySelectorAll('[id^="student-qty-"]');
  equipmentInputs.forEach((input) => (input.value = "0"));

  const submitBtn = document.getElementById("submitStudentBtn");
  if (submitBtn) {
    submitBtn.disabled = false;
    submitBtn.textContent = "Submit Booking";
    submitBtn.style.opacity = "1";
    submitBtn.style.cursor = "pointer";
  }

  const modalFooter = document.querySelector(
    "#studentBookingModal .modal-footer"
  );
  if (modalFooter) modalFooter.style.display = "flex";
}
// Duplicate removed - using robust version from earlier in file

// ========================================
// INLINE VALIDATION HELPERS
// ========================================
function showInlineError(fieldId, message) {
  const field = document.getElementById(fieldId);
  if (!field) return;
  hideInlineError(fieldId);
  field.classList.add("field-error");

  const errorDiv = document.createElement("div");
  errorDiv.className = "inline-error";
  errorDiv.innerHTML = `<span class="error-icon">⚠️</span> ${message}`;
  field.parentNode.appendChild(errorDiv);
}

function hideInlineError(fieldId) {
  const field = document.getElementById(fieldId);
  if (!field) return;
  field.classList.remove("field-error");

  const error = field.parentNode.querySelector(".inline-error");
  if (error) error.remove();
}

// ========================================
// PAGE INITIALIZATION
// ========================================
document.addEventListener("DOMContentLoaded", function () {
  console.log("Student booking page initialized");

  const eventDateField = document.getElementById("eventDate");

  if (eventDateField) {
    const today = new Date().toISOString().split("T")[0];
    eventDateField.setAttribute("min", today);

    // Reload equipment when event date changes (date-based availability)
    eventDateField.addEventListener("change", function () {
      const selectedDate = this.value;
      if (selectedDate) {
        console.log("Event date changed to:", selectedDate);

        // Check facility availability for the selected date
        checkBookingFacilityAvailability(selectedDate);

        // Reset selected equipment when date changes
        selectedStudentEquipment = {};
        const equipmentInputs = document.querySelectorAll(
          '[id^="student-qty-"]'
        );
        equipmentInputs.forEach((input) => (input.value = "0"));

        // Load equipment for the new date
        loadStudentEquipment(selectedDate);
        showToast(
          "📅 Equipment availability updated for selected date",
          "info"
        );
      }
    });
  }
});
