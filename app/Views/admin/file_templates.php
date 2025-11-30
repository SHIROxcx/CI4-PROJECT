<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSPC Admin - File Templates Management</title>
    <link rel="stylesheet" href="<?= base_url('css/admin.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/admin/filetemp.css') ?>">
    <style>
        code {
            background-color: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
            color: #c7254e;
        }

        .clear-file-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            padding: 0;
            line-height: 1;
        }

        .clear-file-btn:hover {
            background-color: #c82333;
            transform: scale(1.1);
        }

        .clear-file-btn:active {
            transform: scale(0.95);
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>CSPC Admin</h3>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li><a href="<?= base_url('/admin') ?>" class="menu-item"><i>📊</i> Dashboard</a></li>
                <li><a href="<?= base_url('/admin/users') ?>" class="menu-item"><i>👥</i> Users</a></li>

                <!-- Dropdown for Booking -->
                <li class="dropdown">
                    <a href="#" class="menu-item dropdown-toggle" onclick="toggleDropdown(event)">
                        <i>🏢</i> Booking <span class="arrow">▾</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?= base_url('/admin/external') ?>" class="submenu-item">👤 Users</a></li>
                        <li><a href="<?= base_url('/admin/student') ?>" class="submenu-item">🎓 Students</a></li>
                    </ul>
                </li>

                <li><a href="<?= base_url('/admin/events') ?>" class="menu-item"><i>📅</i> Events</a></li>
                <li><a href="<?= base_url('/admin/equipment') ?>" class="menu-item"><i>🔧</i> Equipment</a></li>
                <li><a href="<?= base_url('/admin/plans') ?>" class="menu-item"><i>📋</i> Plans</a></li>
                <li><a href="<?= base_url('/admin/facilities-management') ?>" class="menu-item"><i>🏗️</i> Facilities</a></li>

                <div class="sidebar-divider"></div>

                <li><a href="<?= base_url('admin/booking-management') ?>" class="menu-item"><i>📝</i> Bookings</a></li>
                <li><a href="<?= base_url('/admin/attendance') ?>" class="menu-item"><i>📋</i> Attendance</a></li>
                <li><a href="<?= base_url('/admin/file-templates') ?>" class="menu-item active"><i>📄</i> File Templates</a></li>
                
            </ul>
        </div>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar">
                    <?= strtoupper(substr(session('full_name') ?? 'AD', 0, 2)) ?>
                </div>
                <div class="user-details">
                    <?= session('full_name') ?? 'Administrator'; ?>
                    <div class="role">Administrator</div>
                </div>
            </div>
            <a href="<?= site_url('logout') ?>" class="logout-btn" title="Logout">🚪</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

            <div class="search-box">
                <i>🔍</i>
                <input type="text" placeholder="Search templates..." id="searchInput">
            </div>
        </div>


        

        <!-- Dashboard Content -->
        <div class="dashboard">
            <div class="dashboard-title">
                <h2>📄 File Templates Management</h2>
                <p>Manage document templates used for generating booking-related files</p>
            </div>

            <div class="card">
                <div id="alertContainer"></div>

                <div class="templates-grid" id="templatesGrid">
                    <?php if (empty($templates)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">📂</div>
                            <h3>No Template Files Found</h3>
                            <p>No template files found in /public/assets/templates/</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($templates as $template): ?>
                            <div class="template-card" data-filename="<?= esc($template['name']) ?>">
                                <div class="template-icon">
                                    <?php
                                        $icon = '📄';
                                        if ($template['extension'] === 'xlsx' || $template['extension'] === 'xls') {
                                            $icon = '📊';
                                        } elseif ($template['extension'] === 'docx' || $template['extension'] === 'doc') {
                                            $icon = '📝';
                                        } elseif ($template['extension'] === 'pdf') {
                                            $icon = '📕';
                                        }
                                        echo $icon;
                                    ?>
                                </div>
                                <div class="template-name"><?= esc($template['display_name']) ?></div>
                                <div class="template-filename"><?= esc($template['name']) ?></div>
                                <div class="template-info"><strong>Type:</strong> <?= esc($template['type']) ?></div>
                                <div class="template-info"><strong>Size:</strong> <?= esc($template['size_formatted']) ?></div>
                                <div class="template-info"><strong>Modified:</strong> <?= esc($template['modified_formatted']) ?></div>
                                <div class="template-actions">
                                    <button class="btn btn-primary" onclick="openUpdateModal('<?= esc($template['name'], 'js') ?>', '<?= esc($template['display_name'], 'js') ?>', '<?= esc($template['extension'], 'js') ?>')">
                                        Update
                                    </button>
                                    <a href="<?= base_url('admin/file-templates/download/' . urlencode($template['name'])) ?>" class="btn btn-secondary">
                                        Download
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Warning Modal -->
    <div id="warningModal" class="modal">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #fff3cd; border-bottom: 2px solid #ffc107;">
                <h3 style="color: #856404;">⚠️ Important: Template Upload Guidelines</h3>
                <span class="close" onclick="closeWarningModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
                    <h4 style="color: #856404; margin-top: 0;">Please Read Before Uploading:</h4>
                    <ul style="color: #856404; line-height: 1.8;">
                        <li><strong>File Name:</strong> Must keep EXACTLY the same file name as shown (system will reject if different)</li>
                        <li><strong>File Extension:</strong> Must be the same type (.docx, .xlsx, etc.) - system will reject if different</li>
                        <li><strong>Template Variables:</strong> Do NOT modify placeholders like <code>{{variable_name}}</code> or merge fields</li>
                        <li><strong>Document Structure:</strong> Keep the same layout, tables, and formatting structure</li>
                        <li><strong>What You Can Change:</strong> Only update signatories, logos, static text, or styling</li>
                    </ul>
                </div>

                <div style="background-color: #f8d7da; padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545;">
                    <p style="color: #721c24; margin: 0; font-weight: 500;">
                        ⚠️ <strong>Warning:</strong> Modifying template variables or structure will cause the system to generate incorrect or broken documents!
                    </p>
                </div>

                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <button onclick="closeWarningModal()" class="btn btn-secondary" style="flex: 1;">
                        Cancel
                    </button>
                    <button onclick="proceedToUpload()" class="btn btn-primary" style="flex: 1;">
                        I Understand, Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Template Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Update Template</h3>
                <span class="close" onclick="closeUpdateModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="updateTemplateForm" enctype="multipart/form-data">
                    <input type="hidden" id="templateName" name="template_name">

                    <div class="form-group">
                        <label>Template Name <small style="color: #6c757d;">(Read-only - This is the file identifier used by the system)</small></label>
                        <input type="text" id="displayName" class="form-control" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                    </div>

                    <div class="form-group">
                        <label>Upload New File <span id="fileExtension" style="color: var(--primary);"></span></label>
                        <div class="file-upload-wrapper">
                            <div class="file-upload-icon">📤</div>
                            <div class="file-upload-label">
                                Click or drag file here to upload
                            </div>
                            <input type="file" id="templateFile" name="template_file" accept="" required>
                        </div>
                        <div id="selectedFile" class="selected-file" style="display: none;"></div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Upload and Replace Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Dropdown toggle function
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.target.closest('.dropdown');
            dropdown.classList.toggle('open');
        }

        // Sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }

        // Store template data temporarily (declare at top before using)
        let pendingTemplateData = {};

        // Show selected file name and validate
        document.getElementById('templateFile').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const selectedFileDiv = document.getElementById('selectedFile');

            console.log('File selected:', file ? file.name : 'none');
            console.log('Current pendingTemplateData:', pendingTemplateData);

            // Check if pendingTemplateData is available
            if (!pendingTemplateData || !pendingTemplateData.filename || !pendingTemplateData.extension) {
                console.warn('Template data not available for validation');
                if (file) {
                    selectedFileDiv.innerHTML = `<span style="color: #6c757d;">Selected: ${file.name} (${formatFileSize(file.size)})</span>`;
                    selectedFileDiv.style.display = 'block';
                }
                return;
            }

            const expectedFileName = pendingTemplateData.filename;
            const expectedExtension = pendingTemplateData.extension;

            console.log('Expected filename:', expectedFileName);
            console.log('Expected extension:', expectedExtension);

            if (file) {
                const uploadedFileName = file.name;
                const uploadedExtension = uploadedFileName.split('.').pop().toLowerCase();

                // Validate file extension
                if (uploadedExtension !== expectedExtension.toLowerCase()) {
                    selectedFileDiv.innerHTML = `<span style="color: #dc3545;">❌ Wrong file type! Expected: .${expectedExtension}, Got: .${uploadedExtension}</span>`;
                    selectedFileDiv.style.display = 'block';
                    selectedFileDiv.style.color = '#dc3545';
                    e.target.value = ''; // Clear the file input
                    showAlert(`Invalid file type! Please upload a .${expectedExtension} file.`, 'error');
                    return;
                }

                // Validate file name
                if (uploadedFileName.toLowerCase() !== expectedFileName.toLowerCase()) {
                    selectedFileDiv.innerHTML = `<span style="color: #dc3545;">❌ Wrong file name!<br>Expected: <strong>${expectedFileName}</strong><br>Got: <strong>${uploadedFileName}</strong></span>`;
                    selectedFileDiv.style.display = 'block';
                    selectedFileDiv.style.color = '#dc3545';
                    e.target.value = ''; // Clear the file input
                    showAlert(`Invalid file name! The file must be named exactly: ${expectedFileName}`, 'error');
                    return;
                }

                // File is valid
                selectedFileDiv.innerHTML = `
                    <span style="color: #28a745; flex: 1;">✓ Selected: ${file.name} (${formatFileSize(file.size)})</span>
                    <button type="button" onclick="clearSelectedFile()" class="clear-file-btn" title="Remove file">✕</button>
                `;
                selectedFileDiv.style.display = 'flex';
                selectedFileDiv.style.alignItems = 'center';
                selectedFileDiv.style.justifyContent = 'space-between';
                selectedFileDiv.style.gap = '10px';
                selectedFileDiv.style.color = '#28a745';
            } else {
                selectedFileDiv.style.display = 'none';
            }
        });

        function clearSelectedFile() {
            const fileInput = document.getElementById('templateFile');
            const selectedFileDiv = document.getElementById('selectedFile');

            // Clear the file input
            fileInput.value = '';

            // Hide the selected file display
            selectedFileDiv.style.display = 'none';
            selectedFileDiv.innerHTML = '';

            console.log('File selection cleared');
        }

        function openUpdateModal(filename, displayName, extension) {
            // Store the data temporarily
            pendingTemplateData = {
                filename: filename,
                displayName: displayName,
                extension: extension
            };

            console.log('Template data stored:', pendingTemplateData);

            // Show warning modal first
            document.getElementById('warningModal').style.display = 'block';
        }

        function closeWarningModal(clearData = true) {
            document.getElementById('warningModal').style.display = 'none';
            if (clearData) {
                pendingTemplateData = {};
            }
        }

        function proceedToUpload() {
            // Close warning modal WITHOUT clearing data
            closeWarningModal(false);

            console.log('Proceeding to upload with data:', pendingTemplateData);

            // Reset form and hide selected file first
            document.getElementById('updateTemplateForm').reset();
            document.getElementById('selectedFile').style.display = 'none';

            // Then set the values with stored data
            document.getElementById('templateName').value = pendingTemplateData.filename;
            document.getElementById('displayName').value = pendingTemplateData.displayName;
            document.getElementById('fileExtension').textContent = `(.${pendingTemplateData.extension} files only)`;
            document.getElementById('templateFile').accept = `.${pendingTemplateData.extension}`;

            console.log('File extension set to:', pendingTemplateData.extension);
            console.log('File accept attribute:', document.getElementById('templateFile').accept);

            // Finally show the modal
            document.getElementById('updateModal').style.display = 'block';
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').style.display = 'none';
            // Clear data when closing the upload modal
            pendingTemplateData = {};
        }

        window.onclick = function(event) {
            const updateModal = document.getElementById('updateModal');
            const warningModal = document.getElementById('warningModal');

            if (event.target == updateModal) {
                closeUpdateModal();
            }
            if (event.target == warningModal) {
                closeWarningModal();
            }
        }

        document.getElementById('updateTemplateForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Validate file is selected and matches requirements
            const fileInput = document.getElementById('templateFile');
            const file = fileInput.files[0];

            if (!file) {
                showAlert('Please select a file to upload', 'error');
                return;
            }

            // Double-check file name and extension
            if (!pendingTemplateData || !pendingTemplateData.filename || !pendingTemplateData.extension) {
                showAlert('Template data is missing. Please close the modal and try again.', 'error');
                return;
            }

            const expectedFileName = pendingTemplateData.filename;
            const expectedExtension = pendingTemplateData.extension;
            const uploadedFileName = file.name;
            const uploadedExtension = uploadedFileName.split('.').pop().toLowerCase();

            if (uploadedExtension !== expectedExtension.toLowerCase()) {
                showAlert(`Invalid file type! Expected .${expectedExtension} file.`, 'error');
                return;
            }

            if (uploadedFileName.toLowerCase() !== expectedFileName.toLowerCase()) {
                showAlert(`Invalid file name! Must be exactly: ${expectedFileName}`, 'error');
                return;
            }

            const formData = new FormData(this);

            showLoading();

            try {
                const response = await fetch('<?= base_url('admin/file-templates/update') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                hideLoading();

                if (result.success) {
                    showAlert('Template updated successfully!', 'success');
                    closeUpdateModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    let errorMessage = result.message || 'Failed to update template';
                    if (result.details) {
                        errorMessage += '<br><small>' + result.details + '</small>';
                    }
                    showAlert(errorMessage, 'error');
                }
            } catch (error) {
                hideLoading();
                showAlert('An error occurred while updating the template', 'error');
                console.error(error);
            }
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const cards = document.querySelectorAll('.template-card');

            cards.forEach(card => {
                const name = card.querySelector('.template-name').textContent.toLowerCase();
                const filename = card.querySelector('.template-filename').textContent.toLowerCase();

                if (name.includes(searchTerm) || filename.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        function showAlert(message, type) {
            const alertContainer = document.getElementById('alertContainer');
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `<span>${type === 'success' ? '✓' : '✕'}</span>${message}`;
            alertContainer.appendChild(alert);

            setTimeout(() => {
                alert.remove();
            }, 5000);
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function formatFileSize(bytes) {
            if (bytes >= 1048576) {
                return (bytes / 1048576).toFixed(2) + ' MB';
            } else if (bytes >= 1024) {
                return (bytes / 1024).toFixed(2) + ' KB';
            } else {
                return bytes + ' B';
            }
        }
    </script>
</body>
</html>
