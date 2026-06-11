<script>
(function () {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');
    const mobileBtn = document.getElementById('sidebarMobileBtn');
    const storageKey = 'sidebar-collapsed';

    function isMobile() {
        return window.innerWidth < 992;
    }

    function setCollapsed(collapsed) {
        if (isMobile()) return;
        sidebar?.classList.toggle('collapsed', collapsed);
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        localStorage.setItem(storageKey, collapsed ? '1' : '0');
    }

    function openMobile() {
        sidebar?.classList.add('mobile-open');
        backdrop?.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeMobile() {
        sidebar?.classList.remove('mobile-open');
        backdrop?.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (localStorage.getItem(storageKey) === '1' && !isMobile()) {
        setCollapsed(true);
    }

    collapseBtn?.addEventListener('click', () => {
        setCollapsed(!sidebar.classList.contains('collapsed'));
    });

    mobileBtn?.addEventListener('click', () => {
        if (sidebar?.classList.contains('mobile-open')) {
            closeMobile();
        } else {
            openMobile();
        }
    });

    backdrop?.addEventListener('click', closeMobile);

    window.addEventListener('resize', () => {
        if (!isMobile()) {
            closeMobile();
        } else {
            sidebar?.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
        }
    });

    document.querySelectorAll('[data-group-toggle]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (sidebar?.classList.contains('collapsed') && !isMobile()) {
                setCollapsed(false);
                return;
            }

            const target = document.querySelector(btn.dataset.target);
            if (!target) return;

            const isCollapsed = target.classList.contains('collapsed');
            target.classList.toggle('collapsed', !isCollapsed);
            btn.classList.toggle('collapsed', !isCollapsed);
            btn.setAttribute('aria-expanded', isCollapsed ? 'true' : 'false');

            if (isCollapsed) {
                target.style.maxHeight = target.scrollHeight + 'px';
            } else {
                target.style.maxHeight = '0';
            }
        });
    });

    sidebar?.querySelectorAll('.nav-link-item').forEach(link => {
        link.addEventListener('click', () => {
            if (isMobile()) closeMobile();
        });
    });
})();
</script>
