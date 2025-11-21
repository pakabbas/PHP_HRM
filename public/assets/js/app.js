document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('.app-sidebar');
    const toggle = document.getElementById('sidebarToggle');
    if (sidebar && toggle) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    document.querySelectorAll('[data-confirm]').forEach((el) => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.getAttribute('data-confirm'))) {
                e.preventDefault();
            }
        });
    });

    const moduleButtons = document.querySelectorAll('.payroll-nav-btn');
    const modulePanels = document.querySelectorAll('.payroll-module');
    if (moduleButtons.length && modulePanels.length) {
        moduleButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const target = button.getAttribute('data-module-target');
                moduleButtons.forEach((btn) => {
                    const isActive = btn === button;
                    btn.classList.toggle('active', isActive);
                    btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });
                modulePanels.forEach((panel) => {
                    panel.classList.toggle('active', panel.id === `module-${target}`);
                });
                if (window.innerWidth < 992) {
                    const activePanel = document.getElementById(`module-${target}`);
                    if (activePanel) {
                        activePanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            });
        });
    }
});

