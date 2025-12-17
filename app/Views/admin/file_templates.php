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

                <?php if (empty($templates)): ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">📂</div>
                        <h3>No Template Files Found</h3>
                        <p>No template files found in /public/assets/templates/</p>
                    </div>
                <?php else: ?>
                    <div class="templates-grid" id="templatesGrid">
                        <?php foreach ($templates as $template): ?>
                            <?php 
                            // Skip templates without signatories
                            $excludeTemplates = ['report_summary', 'faculty_evaluation', 'report_summary_template', 'faculty_evaluation_template'];
                            $templateBaseName = strtolower(pathinfo($template['name'], PATHINFO_FILENAME));
                            if (in_array($templateBaseName, $excludeTemplates)) {
                                continue;
                            }
                            ?>
                            <div class="template-card" data-filename="<?= esc($template['name']) ?>" onclick="openSignatoriesModal('<?= esc($template['name'], 'js') ?>', '<?= esc($template['display_name'], 'js') ?>')">
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
                                <div style="text-align: center; margin-top: 15px; color: var(--primary); font-size: 12px; font-weight: 600;">
                                    ➜ Click to Edit Signatories
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Signatories Modal -->
    <div id="signatoriesModal" class="modal" style="display: none;">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 id="modalTitle">Edit Signatories</h3>
                <span class="close" onclick="closeSignatoriesModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="signatoriesEditForm">
                    <input type="hidden" id="modalTemplateName" name="template_name">
                    <div id="signatoriesContainer"></div>
                    
                    <div style="margin-top: 30px; display: flex; gap: 10px;">
                        <button type="button" onclick="closeSignatoriesModal()" class="btn btn-secondary" style="flex: 1;">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            💾 Save Changes
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

        // Open signatories modal for a template
        function openSignatoriesModal(templateName, displayName) {
            document.getElementById('modalTitle').textContent = `Edit Signatories - ${displayName}`;
            document.getElementById('modalTemplateName').value = templateName;
            
            showLoading();
            console.log('Loading config for template:', templateName);

            fetch('<?= base_url('admin/file-templates/get-template-config') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    template_name: templateName
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(result => {
                console.log('Response data:', result);
                hideLoading();
                
                if (result.success) {
                    buildSignatoriesForm(result.data);
                    document.getElementById('signatoriesModal').style.display = 'block';
                } else {
                    showAlert('Error: ' + (result.message || 'Failed to load template configuration'), 'error');
                    console.error('Error from server:', result);
                }
            })
            .catch(error => {
                hideLoading();
                showAlert('Error loading template configuration: ' + error.message, 'error');
                console.error('Fetch error:', error);
            });
        }

        // Build the signatory form based on template configuration
        function buildSignatoriesForm(templateConfig) {
            const container = document.getElementById('signatoriesContainer');
            container.innerHTML = '';

            if (!templateConfig.signatories || templateConfig.signatories.length === 0) {
                container.innerHTML = '<p style="color: var(--danger);">No signatories configured for this template</p>';
                return;
            }

            templateConfig.signatories.forEach((sig, index) => {
                const fieldHtml = `
                    <div class="form-group">
                        <label for="signatory_${index}">${sig.label}</label>
                        ${sig.subtitle ? `<small style="color: #6c757d; display: block; margin-bottom: 8px; font-style: italic;">${sig.subtitle}</small>` : ''}
                        <input type="text" 
                               id="signatory_${index}" 
                               name="signatories[${index}]" 
                               class="form-control" 
                               placeholder="${sig.placeholder || 'Enter ' + sig.label.toLowerCase()}"
                               value="${sig.current_value || ''}">
                        <small style="color: #6c757d; display: block; margin-top: 4px;">${sig.cell_location}</small>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', fieldHtml);
            });
        }

        // Close signatories modal
        function closeSignatoriesModal() {
            document.getElementById('signatoriesModal').style.display = 'none';
            document.getElementById('signatoriesEditForm').reset();
        }

        // Handle form submission
        document.getElementById('signatoriesEditForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const templateName = document.getElementById('modalTemplateName').value;
            const formData = new FormData(this);
            
            // Collect signatory values
            const signatories = {};
            const inputs = this.querySelectorAll('input[name^="signatories"]');
            inputs.forEach((input, index) => {
                signatories[index] = input.value;
            });

            console.log('Submitting signatories:', {
                template_name: templateName,
                signatories: signatories
            });

            showLoading();

            try {
                const response = await fetch('<?= base_url('admin/file-templates/update-signatories') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        template_name: templateName,
                        signatories: signatories
                    })
                });

                const result = await response.json();

                hideLoading();

                console.log('Server response:', result);

                if (result.success) {
                    showAlert('✓ Signatories updated successfully!', 'success');
                    closeSignatoriesModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    // Show detailed error message if available
                    let errorMsg = result.message || 'Failed to update signatories';
                    if (result.details) {
                        errorMsg += '\n\nDetails: ' + result.details;
                    }
                    showAlert(errorMsg, 'error');
                    console.error('Update failed:', result);
                }
            } catch (error) {
                hideLoading();
                showAlert('An error occurred while updating signatories', 'error');
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

        // Modal close on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('signatoriesModal');
            if (event.target == modal) {
                closeSignatoriesModal();
            }
        }

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
    </script>
</body>
</html>
