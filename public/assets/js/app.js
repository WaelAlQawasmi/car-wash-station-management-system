// Car Stashen ERP - Premium Frontend Script

document.addEventListener('DOMContentLoaded', function () {
    // 1. Theme Switcher System
    const themeToggle = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;

    // Load initial theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    htmlElement.setAttribute('data-bs-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const currentTheme = htmlElement.getAttribute('data-bs-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        const icon = document.querySelector('#themeToggle i');
        if (icon) {
            if (theme === 'dark') {
                icon.className = 'bi bi-sun-fill';
            } else {
                icon.className = 'bi bi-moon-stars-fill';
            }
        }
    }

    // 2. Sidebar Mobile Drawer Toggle
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('d-none');
        });
    }

    // 3. Client-side Real-time Search everywhere (for listings)
    const searchInputs = document.querySelectorAll('[data-search-table]');
    searchInputs.forEach(input => {
        const targetTableId = input.getAttribute('data-search-table');
        const table = document.getElementById(targetTableId);
        if (table) {
            input.addEventListener('keyup', function () {
                const term = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });

    // 4. Modal Vehicle Loading Handler (AJAX)
    const vehicleModals = document.querySelectorAll('[data-load-vehicles]');
    vehicleModals.forEach(btn => {
        btn.addEventListener('click', function () {
            const customerId = this.getAttribute('data-load-vehicles');
            const targetContainerId = this.getAttribute('data-target-container');
            const container = document.getElementById(targetContainerId);

            // Fetch CSRF & CSRF fields
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            if (container) {
                container.innerHTML = `<tr><td colspan="5" class="text-center"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>`;
                
                fetch(`/customers/vehicles?customer_id=${customerId}`)
                    .then(res => res.json())
                    .then(vehicles => {
                        container.innerHTML = '';
                        if (vehicles.length === 0) {
                            container.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No vehicles associated yet.</td></tr>`;
                            return;
                        }

                        vehicles.forEach(v => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td class="fw-bold">${v.plate_number}</td>
                                <td>${v.brand} ${v.model}</td>
                                <td>${v.year || '-'}</td>
                                <td><span class="badge bg-secondary" style="background-color: ${v.color || '#94a3b8'}">${v.color || 'Unknown'}</span></td>
                                <td>${v.mileage ? v.mileage + ' km' : '-'}</td>
                            `;
                            container.appendChild(tr);
                        });
                    })
                    .catch(err => {
                        container.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Failed to load vehicle list.</td></tr>`;
                    });
            }
        });
    });

    // 5. Success/Error Alert Auto Fadeout
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = bootstrap.Alert.getInstance(alert);
            if (bsAlert) {
                bsAlert.close();
            }
        }, 4000);
    });
});
