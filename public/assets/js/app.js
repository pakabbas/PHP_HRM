document.addEventListener('DOMContentLoaded', () => {
    // Sidebar toggle
    const sidebar = document.querySelector('.app-sidebar');
    const toggle = document.getElementById('sidebarToggle');
    if (sidebar && toggle) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    // Confirmation modals
    document.querySelectorAll('[data-confirm]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            const message = el.getAttribute('data-confirm');
            const href = el.getAttribute('href') || el.closest('form')?.action || '#';
            const method = el.getAttribute('data-method') || 'GET';
            
            const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
            document.getElementById('confirmModalBody').textContent = message;
            document.getElementById('confirmModalBtn').onclick = () => {
                if (method === 'POST') {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = href;
                    const token = document.querySelector('input[name="_token"]')?.value;
                    if (token) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = '_token';
                        input.value = token;
                        form.appendChild(input);
                    }
                    document.body.appendChild(form);
                    form.submit();
                } else {
                    window.location.href = href;
                }
            };
            modal.show();
        });
    });

    // Toast notifications from flash messages
    const flashAlert = document.querySelector('.alert');
    if (flashAlert) {
        const type = flashAlert.classList.contains('alert-success') ? 'success' :
                     flashAlert.classList.contains('alert-danger') ? 'danger' :
                     flashAlert.classList.contains('alert-warning') ? 'warning' : 'info';
        const message = flashAlert.textContent.trim();
        showToast(message, type);
    }

    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});

// Toast notification function
function showToast(message, type = 'info') {
    const toastEl = document.getElementById('toastNotification');
    const toastBody = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    const toastTitle = document.getElementById('toastTitle');
    
    const icons = {
        success: 'bi-check-circle-fill text-success',
        danger: 'bi-exclamation-triangle-fill text-danger',
        warning: 'bi-exclamation-circle-fill text-warning',
        info: 'bi-info-circle-fill text-primary'
    };
    
    const titles = {
        success: 'Success',
        danger: 'Error',
        warning: 'Warning',
        info: 'Information'
    };
    
    toastIcon.className = 'bi ' + icons[type] + ' me-2';
    toastTitle.textContent = titles[type];
    toastBody.textContent = message;
    
    const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
    toast.show();
}

