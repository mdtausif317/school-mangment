<style>
    :root {
        --brand: #0a5f47;
        --brand-dark: #084a38;
        --brand-light: #0d7a5c;
        --sidebar-w: 260px;
        --sidebar-mini: 76px;
        --topbar-h: 60px;
    }

    body { background: #f0f2f5; }

    .app-sidebar {
        width: var(--sidebar-w);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 1040;
        background: linear-gradient(180deg, var(--brand) 0%, var(--brand-dark) 100%);
        color: #fff;
        display: flex;
        flex-direction: column;
        transition: width .25s ease, transform .25s ease;
        box-shadow: 2px 0 12px rgba(0,0,0,.08);
    }

    .app-sidebar.collapsed { width: var(--sidebar-mini); }

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1rem;
        min-height: var(--topbar-h);
        border-bottom: 1px solid rgba(255,255,255,.12);
        flex-shrink: 0;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: .65rem;
        font-weight: 600;
        font-size: .95rem;
        white-space: nowrap;
        overflow: hidden;
        min-width: 0;
    }

    .sidebar-brand-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255,255,255,.15);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sidebar-brand-text {
        opacity: 1;
        transition: opacity .2s;
    }

    .app-sidebar.collapsed .sidebar-brand-text { opacity: 0; width: 0; }

    .sidebar-toggle {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 8px;
        background: rgba(255,255,255,.12);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
        transition: background .2s;
    }

    .sidebar-toggle:hover { background: rgba(255,255,255,.22); }

    .app-sidebar.collapsed .sidebar-toggle i { transform: rotate(180deg); }

    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        padding: .75rem .65rem 1rem;
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,.3) transparent;
    }

    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.25); border-radius: 4px; }

    .nav-link-item {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .6rem .85rem;
        margin-bottom: 2px;
        border-radius: 10px;
        color: rgba(255,255,255,.88);
        text-decoration: none;
        font-size: .9rem;
        transition: background .15s, color .15s;
        white-space: nowrap;
    }

    .nav-link-item:hover {
        background: rgba(255,255,255,.12);
        color: #fff;
    }

    .nav-link-item.active {
        background: rgba(255,255,255,.18);
        color: #fff;
        font-weight: 500;
        box-shadow: inset 3px 0 0 #fff;
    }

    .nav-link-item.sub { padding-left: 1.5rem; font-size: .875rem; }

    .app-sidebar.collapsed .nav-link-item.sub { padding-left: .85rem; }

    .nav-link-icon {
        width: 20px;
        text-align: center;
        flex-shrink: 0;
        font-size: .95rem;
        opacity: .9;
    }

    .nav-link-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: opacity .2s;
    }

    .app-sidebar.collapsed .nav-link-text { opacity: 0; width: 0; }

    .app-sidebar.collapsed .nav-link-item,
    .app-sidebar.collapsed .nav-group-toggle {
        justify-content: center;
        padding-left: .85rem;
        padding-right: .85rem;
    }

    .app-sidebar.collapsed .nav-link-item.sub { padding-left: .85rem; }

    .nav-group { margin-bottom: 4px; }

    .nav-group-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .6rem .85rem;
        border: none;
        border-radius: 10px;
        background: transparent;
        color: rgba(255,255,255,.88);
        font-size: .9rem;
        font-weight: 500;
        cursor: pointer;
        transition: background .15s, color .15s;
        text-align: left;
    }

    .nav-group-toggle:hover { background: rgba(255,255,255,.08); color: #fff; }

    .nav-group-toggle .nav-chevron {
        margin-left: auto;
        font-size: .65rem;
        transition: transform .25s;
    }

    .nav-group-toggle:not(.collapsed) .nav-chevron { transform: rotate(180deg); }

    .app-sidebar.collapsed .nav-group-toggle .nav-link-text,
    .app-sidebar.collapsed .nav-group-toggle .nav-chevron { display: none; }

    .nav-group-items {
        overflow: hidden;
        transition: max-height .3s ease;
    }

    .nav-group-items.collapsed { max-height: 0 !important; }

    .sidebar-empty {
        padding: .75rem 1rem;
        font-size: .8rem;
        color: rgba(255,255,255,.5);
    }

    .main-content {
        margin-left: var(--sidebar-w);
        min-height: 100vh;
        transition: margin-left .25s ease;
    }

    body.sidebar-collapsed .main-content { margin-left: var(--sidebar-mini); }

    .top-bar {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: 0 1.25rem;
        min-height: var(--topbar-h);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .top-bar-left { display: flex; align-items: center; gap: .75rem; }

    .top-bar-title { font-weight: 600; font-size: 1.05rem; color: #1f2937; }

    .user-pill {
        display: flex;
        align-items: center;
        gap: .75rem;
    }

    .user-pill .name { font-size: .875rem; color: #6b7280; }

    .btn-brand { background: var(--brand); color: #fff; border: none; }
    .btn-brand:hover { background: var(--brand-light); color: #fff; }

    .sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 1035;
    }

    .sidebar-backdrop.show { display: block; }

    @media (max-width: 991.98px) {
        .app-sidebar {
            transform: translateX(-100%);
            width: var(--sidebar-w) !important;
        }

        .app-sidebar.mobile-open {
            transform: translateX(0);
        }

        .app-sidebar.collapsed .sidebar-brand-text,
        .app-sidebar.collapsed .nav-link-text { opacity: 1; width: auto; }

        .main-content,
        body.sidebar-collapsed .main-content { margin-left: 0; }

        .sidebar-toggle-desktop { display: none !important; }

        .user-pill .name { display: none; }
    }

    @media (min-width: 992px) {
        .sidebar-toggle-mobile { display: none !important; }
    }
</style>
