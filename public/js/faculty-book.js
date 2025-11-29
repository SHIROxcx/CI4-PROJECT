// ==================== GLOBAL VARIABLES ====================
let selectedBookingType = null; // 'free' or 'paid'
let currentFacilityKey = null;
let currentFacilityId = null;
let facilityData = {};
let addonsData = [];
let equipmentData = [];
let selectedPlan = null;
let selectedAddons = [];
let selectedEquipment = {};
let MAINTENANCE_FEE = 2000;
let HOURLY_RATE = 500;

// ==================== BOOKING TYPE SELECTION ====================
function selectBookingType(type) {
    selectedBookingType = type;

    // Update UI
    document.getElementById('freeBookingCard').classList.remove('selected');
    document.getElementById('paidBookingCard').classList.remove('selected');

    if (type === 'free') {
        document.getElementById('freeBookingCard').classList.add('selected');
        document.getElementById('selectedTypeText').textContent = 'Academic/Free Booking - No payment required';
    } else {
        document.getElementById('paidBookingCard').classList.add('selected');
        document.getElementById('selectedTypeText').textContent = 'Commercial/Paid Booking - Standard rates apply';
    }

    // Show selected type and facilities
    document.getElementById('selectedTypeDisplay').style.display = 'block';
    document.getElementById('facilitiesGrid').style.display = 'grid';

    // Update price display on facility cards
    updateFacilityPriceDisplay();
}

function clearBookingType() {
    selectedBookingType = null;
    document.getElementById('freeBookingCard').classList.remove('selected');
    document.getElementById('paidBookingCard').classList.remove('selected');
    document.getElementById('selectedTypeDisplay').style.display = 'none';
    document.getElementById('facilitiesGrid').style.display = 'none';
}

function updateFacilityPriceDisplay() {
    const priceRanges = document.querySelectorAll('.price-range');
    priceRanges.forEach(el => {
        if (selectedBookingType === 'free') {
            el.textContent = 'FREE (Academic)';
            el.style.color = '#16a34a';
        } else {
            el.textContent = 'View Packages';
            el.style.color = '#f59e0b';
        }
    });
}

// ==================== OPEN BOOKING MODAL ====================
function openFacultyBookingModal(facilityKey, facilityId) {
    if (!selectedBookingType) {
        alert('Please select a booking type first (Free or Paid)');
        return;
    }

    currentFacilityKey = facilityKey;
    currentFacilityId = facilityId;

    if (selectedBookingType === 'free') {
        openFreeBookingModal(facilityKey, facilityId);
    } else {
        openPaidBookingModal(facilityKey, facilityId);
    }
}

// ==================== FREE BOOKING FUNCTIONS ====================
function openFreeBookingModal(facilityKey, facilityId) {
    document.getElementById('freeFacilityKey').value = facilityKey;
    document.getElementById('freeFacilityId').value = facilityId;

    // Load equipment for free booking
    loadFreeEquipment();

    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('freeEventDate').min = today;

    // Show modal
    document.getElementById('freeBookingModal').style.display = 'block';

    // Enable form validation
    validateFreeForm();
}

function closeFreeModal() {
    document.getElementById('freeBookingModal').style.display = 'none';
    document.getElementById('freeBookingForm').reset();
}

async function loadFreeEquipment() {
    try {
        const response = await fetch('/api/bookings/equipment');
        const result = await response.json();

        if (result.success) {
            const freeEquipmentGrid = document.getElementById('freeEquipmentGrid');
            freeEquipmentGrid.innerHTML = '';

            // Filter for non-rentable equipment (free equipment)
            const freeEquipment = result.equipment.filter(eq =>
                eq.is_rentable == 0 || eq.rate == 0
            );

            if (freeEquipment.length === 0) {
                freeEquipmentGrid.innerHTML = '<p class="text-muted">No equipment available for selection.</p>';
                return;
            }

            freeEquipment.forEach(equipment => {
                const equipmentCard = document.createElement('div');
                equipmentCard.className = 'col-md-6';
                equipmentCard.innerHTML = `
                    <div class="equipment-item mb-3">
                        <label class="form-label">
                            ${equipment.name}
                            <span class="text-success">(FREE)</span>
                        </label>
                        <input type="number" class="form-control"
                               id="free-qty-${equipment.id}"
                               min="0" max="${equipment.available}" value="0"
                               onchange="updateFreeEquipment('${equipment.id}')">
                        <small class="text-muted">Available: ${equipment.available}</small>
                    </div>
                `;
                freeEquipmentGrid.appendChild(equipmentCard);
            });
        }
    } catch (error) {
        console.error('Error loading free equipment:', error);
    }
}

function updateFreeEquipment(equipmentId) {
    validateFreeForm();
}

function handleFreeFileSelect(input, docType) {
    const file = input.files[0];
    const uploadItem = document.getElementById(`free-upload-${docType}`);
    const statusSpan = uploadItem.querySelector('.upload-status');
    const filenameDisplay = document.getElementById(`free-filename-${docType}`);

    if (file) {
        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('File size must be less than 10MB');
            input.value = '';
            return;
        }

        statusSpan.textContent = 'Uploaded';
        statusSpan.style.color = '#16a34a';
        filenameDisplay.textContent = `File: ${file.name}`;
        uploadItem.style.background = '#f0fdf4';
    } else {
        statusSpan.textContent = 'Not uploaded';
        statusSpan.style.color = '#dc2626';
        filenameDisplay.textContent = '';
        uploadItem.style.background = '';
    }

    validateFreeForm();
}

function validateFreeForm() {
    const form = document.getElementById('freeBookingForm');
    const submitBtn = document.getElementById('submitFreeBtn');

    // Check if all required files are uploaded
    const permissionFile = document.getElementById('free-file-permission').files[0];
    const requestFile = document.getElementById('free-file-request').files[0];
    const approvalFile = document.getElementById('free-file-approval').files[0];

    const allFilesUploaded = permissionFile && requestFile && approvalFile;
    const formValid = form.checkValidity();

    submitBtn.disabled = !(formValid && allFilesUploaded);
}

async function submitFreeBooking() {
    const form = document.getElementById('freeBookingForm');
    const submitBtn = document.getElementById('submitFreeBtn');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Processing...';

    try {
        showToast('Validating booking details...', 'info');

        // Get equipment selections
        const equipmentSelections = {};
        document.querySelectorAll('[id^="free-qty-"]').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const equipmentId = input.id.replace('free-qty-', '');
                equipmentSelections[equipmentId] = qty;
            }
        });

        // STEP 1: Create the booking with JSON data
        const bookingData = {
            facility_id: parseInt(document.getElementById('freeFacilityId').value),
            plan_id: 1, // Free plan (you may need to adjust this)
            client_name: document.getElementById('freeClientName').value,
            email_address: document.getElementById('freeClientEmail').value,
            organization: document.getElementById('freeOrganization').value,
            contact_number: document.getElementById('freeContactNumber').value,
            address: document.getElementById('freeAddress').value || '',
            event_date: document.getElementById('freeEventDate').value,
            event_time: document.getElementById('freeEventTime').value,
            duration: parseInt(document.getElementById('freeDuration').value),
            attendees: parseInt(document.getElementById('freeAttendees').value) || null,
            event_title: document.getElementById('freeEventTitle').value,
            special_requirements: document.getElementById('freeSpecialRequirements').value || '',
            selected_equipment: equipmentSelections
        };

        console.log('Sending booking data:', bookingData);

        const bookingResponse = await fetch('/api/student/bookings/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(bookingData)
        });

        const bookingResult = await bookingResponse.json();
        console.log('Booking result:', bookingResult);

        if (!bookingResult.success) {
            throw new Error(bookingResult.message || 'Failed to create booking');
        }

        const bookingId = bookingResult.booking_id;
        showToast(`Booking created! ID: BK${String(bookingId).padStart(3, '0')}`, 'success');

        // STEP 2: Upload files if any are selected
        const permissionFile = document.getElementById('free-file-permission').files[0];
        const requestFile = document.getElementById('free-file-request').files[0];
        const approvalFile = document.getElementById('free-file-approval').files[0];
        const hasFiles = permissionFile || requestFile || approvalFile;

        if (hasFiles) {
            try {
                const formData = new FormData();
                if (permissionFile) formData.append('files[]', permissionFile);
                if (requestFile) formData.append('files[]', requestFile);
                if (approvalFile) formData.append('files[]', approvalFile);

                showToast('Uploading documents...', 'info');

                const uploadResponse = await fetch(`/api/student/bookings/${bookingId}/upload`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const uploadResult = await uploadResponse.json();
                if (!uploadResult.success) {
                    showToast('Booking created but some files failed to upload', 'warning');
                } else {
                    showToast(`${uploadResult.files.length} file(s) uploaded successfully`, 'success');
                }
            } catch (uploadError) {
                console.error('File upload error:', uploadError);
                showToast('Booking created but file upload failed', 'warning');
            }
        }

        // SUCCESS!
        closeFreeModal();
        showToast(`Booking submitted successfully! Reference: BK${String(bookingId).padStart(3, '0')}`, 'success');

        setTimeout(() => {
            window.location.href = '/faculty/bookings';
        }, 2000);

    } catch (error) {
        console.error('Error submitting free booking:', error);
        showToast(error.message || 'An error occurred while submitting your booking. Please try again.', 'error');

        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Free Booking';
    }
}

// ==================== PAID BOOKING FUNCTIONS (LIKE EXTERNAL.PHP) ====================

async function openPaidBookingModal(facilityKey, facilityId) {
    currentFacilityKey = facilityKey;
    currentFacilityId = facilityId;

    // Load facility data
    await loadPaidFacilityData(facilityKey);

    document.getElementById('paidFacilityKey').value = facilityKey;
    document.getElementById('paidFacilityId').value = facilityId;

    const facility = facilityData[facilityKey];

    if (!facility) {
        showToast('Facility data not found', 'error');
        return;
    }

    // Update the hourly rate for this specific facility
    if (facility.additional_hours_rate) {
        HOURLY_RATE = parseFloat(facility.additional_hours_rate);
    }

    // Update the hourly rate label
    const hourlyRateLabel = document.getElementById('paidHourlyRateLabel');
    if (hourlyRateLabel) {
        hourlyRateLabel.textContent = `₱${HOURLY_RATE.toLocaleString()}`;
    }

    document.getElementById('paidModalTitle').textContent = `Book ${facility.name}`;
    document.getElementById('paidBookingModal').style.display = 'block';

    // Populate plans
    populatePaidPlans(facility.plans);

    // Populate add-ons
    populatePaidAddons();

    // Show date prompt for equipment
    showPaidEquipmentDatePrompt();

    // Reset selections
    selectedPlan = null;
    selectedAddons = [];
    selectedEquipment = {};
    document.getElementById('paidAdditionalHours').value = 0;
    document.getElementById('paidEventDate').value = '';
    updatePaidCostSummary();

    // Set minimum date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('paidEventDate').min = today;
}

function closePaidModal() {
    document.getElementById('paidBookingModal').style.display = 'none';
    document.getElementById('paidBookingForm').reset();
}

async function loadPaidFacilityData(facilityKey) {
    try {
        const response = await fetch(`/api/facilities/${facilityKey}/data`);
        const data = await response.json();

        if (data.success && data.facility) {
            facilityData[facilityKey] = data.facility;

            // Also load addons and equipment
            await loadPaidAddonsData();
        }
    } catch (error) {
        console.error('Error loading facility data:', error);
    }
}

async function loadPaidAddonsData() {
    try {
        const response = await fetch('/api/addons');
        const data = await response.json();

        addonsData = data
            .filter(addon => addon.addon_key !== 'additional-hours')
            .map(addon => ({
                id: addon.addon_key,
                name: addon.name,
                description: addon.description,
                price: parseFloat(addon.price)
            }));
    } catch (error) {
        console.error('Error loading addons:', error);
    }
}

function populatePaidPlans(plans) {
    const plansGrid = document.getElementById('paidPlansGrid');
    if (!plansGrid || !plans) return;

    plansGrid.innerHTML = '';

    plans.forEach(plan => {
        const planCard = document.createElement('div');
        planCard.className = 'plan-card';
        planCard.onclick = () => selectPaidPlan(plan);

        const features = plan.features || [];
        const includedEquipment = plan.included_equipment || [];

        let featuresList = features.map(f => `<li><span class="check">✓</span> ${f}</li>`).join('');
        let equipmentList = includedEquipment.map(eq =>
            `<li><span class="check">✓</span> ${eq.quantity_included} ${eq.unit} - ${eq.name}</li>`
        ).join('');

        planCard.innerHTML = `
            <div class="plan-header">
                <h3 class="plan-name">${plan.name}</h3>
                <div class="plan-price">₱${parseFloat(plan.price).toLocaleString()}</div>
                <p class="plan-duration">${plan.duration}</p>
            </div>
            <div class="plan-features">
                <ul>
                    ${featuresList}
                    ${equipmentList}
                </ul>
            </div>
        `;

        plansGrid.appendChild(planCard);
    });
}

function selectPaidPlan(plan) {
    selectedPlan = plan;
    document.getElementById('paidSelectedPlanId').value = plan.id;

    // Update UI to show selected plan
    document.querySelectorAll('.plan-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.target.closest('.plan-card').classList.add('selected');

    updatePaidCostSummary();
    showToast(`Selected: ${plan.name}`, 'success');
}

function populatePaidAddons() {
    const addonsGrid = document.getElementById('paidAddonsGrid');
    if (!addonsGrid) return;

    addonsGrid.innerHTML = '';

    if (!addonsData || addonsData.length === 0) {
        addonsGrid.innerHTML = '<p style="color: #6c757d;">No add-ons available.</p>';
        return;
    }

    addonsData.forEach(addon => {
        const addonCard = document.createElement('div');
        addonCard.className = 'addon-card';

        addonCard.innerHTML = `
            <div class="addon-checkbox">
                <input type="checkbox" id="paid-addon-${addon.id}" onchange="togglePaidAddon('${addon.id}')">
            </div>
            <div class="addon-info">
                <h4 class="addon-name">${addon.name}</h4>
                <p class="addon-description">${addon.description || ''}</p>
            </div>
            <div class="addon-price">₱${addon.price.toLocaleString()}</div>
        `;

        addonsGrid.appendChild(addonCard);
    });
}

function togglePaidAddon(addonId) {
    const checkbox = document.getElementById(`paid-addon-${addonId}`);

    if (checkbox.checked) {
        selectedAddons.push(addonId);
    } else {
        selectedAddons = selectedAddons.filter(id => id !== addonId);
    }

    updatePaidCostSummary();
}

function showPaidEquipmentDatePrompt() {
    const equipmentGrid = document.getElementById('paidEquipmentGrid');
    const placeholder = document.getElementById('paidEquipmentDatePlaceholder');

    if (equipmentGrid) equipmentGrid.style.display = 'none';
    if (placeholder) placeholder.style.display = 'block';
}

async function handlePaidDateChange() {
    const eventDate = document.getElementById('paidEventDate').value;

    if (eventDate) {
        // Load equipment for this date
        await loadPaidEquipmentForDate(eventDate);

        // Show equipment grid
        document.getElementById('paidEquipmentGrid').style.display = 'grid';
        document.getElementById('paidEquipmentDatePlaceholder').style.display = 'none';
    }
}

async function loadPaidEquipmentForDate(eventDate) {
    try {
        const response = await fetch('/api/bookings/equipment-availability', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                event_date: eventDate,
                facility_id: currentFacilityId
            })
        });

        const result = await response.json();

        if (result.success && result.equipment) {
            // Filter for rentable equipment only
            equipmentData = result.equipment
                .filter(eq => (eq.is_rentable == 1 && parseFloat(eq.rate || 0) > 0))
                .filter(eq => eq.category === 'furniture' || eq.category === 'logistics')
                .map(equipment => ({
                    id: equipment.id.toString(),
                    name: equipment.name,
                    rate: parseFloat(equipment.rate || 0),
                    unit: equipment.unit || 'piece',
                    available: parseInt(equipment.available_on_date || 0),
                    category: equipment.category
                }));

            populatePaidEquipment();
        }
    } catch (error) {
        console.error('Error loading equipment:', error);
    }
}

function populatePaidEquipment() {
    const equipmentGrid = document.getElementById('paidEquipmentGrid');
    if (!equipmentGrid) return;

    equipmentGrid.innerHTML = '';

    if (!equipmentData || equipmentData.length === 0) {
        equipmentGrid.innerHTML = '<p style="color: #6c757d;">No rental equipment available.</p>';
        return;
    }

    equipmentData.forEach(equipment => {
        const equipmentCard = document.createElement('div');
        equipmentCard.className = 'equipment-card';

        const isAvailable = equipment.available > 0;

        equipmentCard.innerHTML = `
            <div class="equipment-info">
                <h4 class="equipment-name">${equipment.name}</h4>
                <p class="equipment-description">${equipment.category}</p>
                <span class="equipment-price">₱${equipment.rate.toLocaleString()}/${equipment.unit}</span>
            </div>
            <div class="equipment-actions-card">
                ${!isAvailable
                    ? `<input type="number" class="form-control qty-input" value="0" disabled>
                       <label class="equipment-label" style="color: #dc2626;">Out of Stock</label>`
                    : `<input type="number" class="form-control qty-input"
                              id="paid-qty-${equipment.id}"
                              min="0" max="${equipment.available}" value="0"
                              onchange="updatePaidEquipment('${equipment.id}')">
                       <label class="equipment-label">Available: ${equipment.available}</label>`
                }
            </div>
        `;

        equipmentGrid.appendChild(equipmentCard);
    });
}

function updatePaidEquipment(equipmentId) {
    const quantityInput = document.getElementById(`paid-qty-${equipmentId}`);
    const quantity = parseInt(quantityInput.value) || 0;
    const equipment = equipmentData.find(e => e.id === equipmentId);

    if (!equipment) return;

    if (quantity > equipment.available) {
        alert(`Only ${equipment.available} units available for ${equipment.name}`);
        quantityInput.value = equipment.available;
        selectedEquipment[equipmentId] = equipment.available;
    } else if (quantity > 0) {
        selectedEquipment[equipmentId] = quantity;
    } else {
        delete selectedEquipment[equipmentId];
    }

    updatePaidCostSummary();
}

function updatePaidCostSummary() {
    let basePrice = selectedPlan ? selectedPlan.price : 0;

    // Calculate addon cost
    let addonsPrice = 0;
    selectedAddons.forEach(addonId => {
        const addon = addonsData.find(a => a.id === addonId);
        if (addon) addonsPrice += addon.price;
    });

    // Calculate equipment cost
    let equipmentPrice = 0;
    Object.keys(selectedEquipment).forEach(equipmentId => {
        const equipment = equipmentData.find(e => e.id === equipmentId);
        const quantity = selectedEquipment[equipmentId];
        if (equipment && quantity > 0) {
            equipmentPrice += equipment.rate * quantity;
        }
    });

    // Calculate additional hours cost
    const additionalHours = parseInt(document.getElementById('paidAdditionalHours')?.value) || 0;
    const additionalHoursPrice = additionalHours * HOURLY_RATE;

    // Update display
    document.getElementById('paidBaseCost').textContent = `₱${basePrice.toLocaleString()}`;
    document.getElementById('paidMaintenanceCost').textContent = `₱${MAINTENANCE_FEE.toLocaleString()}`;

    // Build addon costs display
    let addonCostsHTML = '';
    if (addonsPrice > 0) {
        addonCostsHTML += `<div class="cost-row"><span>Add-ons:</span><span>₱${addonsPrice.toLocaleString()}</span></div>`;
    }
    if (equipmentPrice > 0) {
        addonCostsHTML += `<div class="cost-row"><span>Equipment:</span><span>₱${equipmentPrice.toLocaleString()}</span></div>`;
    }
    if (additionalHoursPrice > 0) {
        addonCostsHTML += `<div class="cost-row"><span>Additional Hours:</span><span>₱${additionalHoursPrice.toLocaleString()}</span></div>`;
    }
    document.getElementById('paidAddonCosts').innerHTML = addonCostsHTML;

    const total = basePrice + addonsPrice + equipmentPrice + additionalHoursPrice + MAINTENANCE_FEE;
    document.getElementById('paidTotalCost').textContent = `₱${total.toLocaleString()}`;
}

async function submitPaidBooking() {
    const form = document.getElementById('paidBookingForm');

    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    if (!selectedPlan) {
        showToast('Please select a package first.', 'error');
        return;
    }

    const additionalHours = parseInt(document.getElementById('paidAdditionalHours')?.value) || 0;
    const durationMatch = selectedPlan.duration.match(/\d+/);
    const planDuration = durationMatch ? parseInt(durationMatch[0]) : 0;
    const totalDuration = planDuration + additionalHours;

    // Calculate total cost
    let basePrice = selectedPlan.price;
    let addonsPrice = 0;
    selectedAddons.forEach(addonId => {
        const addon = addonsData.find(a => a.id === addonId);
        if (addon) addonsPrice += addon.price;
    });

    let equipmentPrice = 0;
    Object.keys(selectedEquipment).forEach(equipmentId => {
        const equipment = equipmentData.find(e => e.id === equipmentId);
        const quantity = selectedEquipment[equipmentId];
        if (equipment && quantity > 0) {
            equipmentPrice += equipment.rate * quantity;
        }
    });

    const additionalHoursPrice = additionalHours * HOURLY_RATE;
    const totalCost = basePrice + addonsPrice + equipmentPrice + additionalHoursPrice + MAINTENANCE_FEE;

    const formData = {
        facility_key: currentFacilityKey,
        plan_id: selectedPlan.id,
        client_name: document.getElementById('paidClientName').value,
        contact_number: document.getElementById('paidContactNumber').value,
        email_address: document.getElementById('paidEmailAddress').value,
        organization: document.getElementById('paidOrganization').value,
        address: document.getElementById('paidAddress').value,
        event_date: document.getElementById('paidEventDate').value,
        event_time: document.getElementById('paidEventTime').value,
        duration: totalDuration,
        attendees: document.getElementById('paidAttendees').value || null,
        event_title: document.getElementById('paidEventTitle').value,
        special_requirements: document.getElementById('paidSpecialRequirements').value,
        selected_addons: selectedAddons,
        selected_equipment: selectedEquipment,
        additional_hours: additionalHours,
        maintenance_fee: MAINTENANCE_FEE,
        total_cost: totalCost
    };

    try {
        const response = await fetch('/api/bookings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById('paidReferenceNumber').textContent = 'BK' + String(result.booking_id).padStart(3, '0');
            closePaidModal();

            // Show Bootstrap success modal
            const successModal = new bootstrap.Modal(document.getElementById('paidSuccessModal'));
            successModal.show();

            // Reset form
            form.reset();
            selectedPlan = null;
            selectedAddons = [];
            selectedEquipment = {};
        } else {
            showToast(result.message || 'Failed to create booking', 'error');
        }
    } catch (error) {
        console.error('Error submitting paid booking:', error);
        showToast('An error occurred while submitting your booking. Please try again.', 'error');
    }
}

function closePaidSuccessModal() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('paidSuccessModal'));
    if (modal) modal.hide();
    window.location.href = '/faculty/bookings';
}

// ==================== UTILITY FUNCTIONS ====================

function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();

    const toastId = 'toast-' + Date.now();
    const icons = {
        error: '❌',
        success: '✅',
        warning: '⚠️',
        info: 'ℹ️'
    };

    const toastHTML = `
        <div id="${toastId}" class="toast-notification toast-${type}">
            <div class="toast-content">
                <span class="toast-icon">${icons[type]}</span>
                <span class="toast-message">${message}</span>
            </div>
            <div class="toast-close" onclick="closeToast('${toastId}')">×</div>
        </div>
    `;

    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    setTimeout(() => closeToast(toastId), 5000);
}

function createToastContainer() {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        `;
        document.body.appendChild(container);
    }
    return container;
}

function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }
}

// ==================== FORM VALIDATION LISTENERS ====================
document.addEventListener('DOMContentLoaded', function() {
    // Free form validation
    const freeFormInputs = document.querySelectorAll('#freeBookingForm input, #freeBookingForm textarea');
    freeFormInputs.forEach(input => {
        input.addEventListener('input', validateFreeForm);
        input.addEventListener('change', validateFreeForm);
    });
});
