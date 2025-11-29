// Extension Request Management
let extensionModalInstance = null;
let currentExtensionBookingData = null;

/**
 * Open extension request modal for a specific booking
 */
async function openExtensionRequestModal(bookingId) {
  try {
    // Fetch booking data from API to ensure we have the latest data
    const response = await fetch(`/user/bookings/details/${bookingId}`);
    const data = await response.json();

    if (!data.success) {
      showAlert("error", data.message || "Booking not found");
      return;
    }

    const booking = data.booking;

    // Store current booking data for cost calculation
    currentExtensionBookingData = booking;

    // Set booking ID in hidden input
    document.getElementById("extensionBookingId").value = bookingId;

    // Reset form
    document.getElementById("extensionHours").value = 1;
    document.getElementById("extensionReason").value = "";
    document.getElementById("extensionErrorAlert").style.display = "none";
    document.getElementById("submitExtensionBtn").disabled = false;

    // Set hourly rate from booking facility data
    const hourlyRate = booking.hourly_rate || 0;
    document.getElementById("extensionHourlyRate").textContent = `₱${parseFloat(
      hourlyRate
    ).toLocaleString()}`;

    // Calculate initial cost
    calculateExtensionCost();

    // Show modal
    const modal = new bootstrap.Modal(
      document.getElementById("extensionRequestModal")
    );
    modal.show();
    extensionModalInstance = modal;
  } catch (error) {
    console.error("Error opening extension modal:", error);
    showAlert("error", "Failed to open extension request modal");
  }
}

/**
 * Calculate extension cost based on hours and hourly rate
 */
function calculateExtensionCost() {
  try {
    const hours =
      parseInt(document.getElementById("extensionHours").value) || 1;
    const booking = currentExtensionBookingData;

    if (!booking) {
      console.error("Booking data not available");
      return;
    }

    const hourlyRate = parseFloat(booking.hourly_rate || 0);
    const totalCost = hours * hourlyRate;

    // Update display
    document.getElementById("extensionHoursDisplay").textContent = hours;
    document.getElementById(
      "extensionTotalCost"
    ).textContent = `₱${totalCost.toLocaleString()}`;

    // Enable/disable submit button based on validity
    const submitBtn = document.getElementById("submitExtensionBtn");
    if (hours >= 1 && hours <= 12 && totalCost > 0) {
      submitBtn.disabled = false;
    } else {
      submitBtn.disabled = true;
    }
  } catch (error) {
    console.error("Error calculating extension cost:", error);
  }
}

/**
 * Submit extension request to API
 */
async function submitExtensionRequest() {
  try {
    const bookingId = document.getElementById("extensionBookingId").value;
    const extensionHours = parseInt(
      document.getElementById("extensionHours").value
    );
    const reason = document.getElementById("extensionReason").value.trim();

    // Validation
    if (!bookingId) {
      showAlert("error", "Booking ID is missing");
      return;
    }

    if (extensionHours < 1 || extensionHours > 12) {
      showAlert("error", "Please select between 1 and 12 hours");
      return;
    }

    // Disable button and show loading state
    const submitBtn = document.getElementById("submitExtensionBtn");
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML =
      '<i class="fas fa-spinner fa-spin"></i> Submitting...';

    // Send request to API
    const response = await fetch("/api/extensions/request", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify({
        booking_id: bookingId,
        extension_hours: extensionHours,
        reason: reason || null,
      }),
    });

    const data = await response.json();

    if (data.success) {
      showAlert(
        "success",
        `Extension request submitted! Cost: ₱${parseFloat(
          data.extension_cost
        ).toLocaleString()}`
      );

      // Close modal
      if (extensionModalInstance) {
        extensionModalInstance.hide();
      }

      // Reload bookings to show updated status
      setTimeout(() => {
        loadBookings();
      }, 1000);
    } else {
      showAlert("error", data.message || "Failed to submit extension request");
      // Reset button
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalText;
    }
  } catch (error) {
    console.error("Error submitting extension request:", error);
    showAlert("error", "Failed to submit extension request");

    // Reset button
    const submitBtn = document.getElementById("submitExtensionBtn");
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="fas fa-check"></i> Submit Request';
  }
}

/**
 * Add extension request button to booking details modal (to be called from viewBookingDetails)
 */
function getExtensionButton(booking) {
  // Only show extension button for confirmed bookings
  if (booking.status !== "confirmed" && booking.status !== "pending") {
    return "";
  }

  return `
    <button type="button" class="btn btn-info" onclick="openExtensionRequestModal(${booking.id})">
      <i class="fas fa-clock"></i> Request Extension
    </button>
  `;
}
