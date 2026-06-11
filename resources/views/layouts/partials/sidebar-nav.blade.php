@php
    $sidebarMenu = $sidebarMenu ?? collect();
    $brandIcon = $brandIcon ?? 'fas fa-school';
    $brandText = $brandText ?? ($user->school->name ?? 'School');
@endphp

<aside class="app-sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <span class="sidebar-brand-icon"><i class="{{ $brandIcon }}"></i></span>
            <span class="sidebar-brand-text">{{ $brandText }}</span>
        </div>
        <button type="button" class="sidebar-toggle sidebar-toggle-desktop" id="sidebarCollapseBtn" title="Collapse sidebar">
            <i class="fas fa-angles-left"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        @forelse($sidebarMenu as $menu)
            @if($menu->children->isNotEmpty())
                @php
                    $hasActiveChild = $menu->children->contains(fn ($c) => $c->route_name && $accessMenu->isMenuActive($c));
                    $groupId = 'nav-group-'.$menu->id;
                @endphp
                <div class="nav-group">
                    <button type="button"
                            class="nav-group-toggle {{ $hasActiveChild ? '' : 'collapsed' }}"
                            data-group-toggle
                            data-target="#{{ $groupId }}"
                            aria-expanded="{{ $hasActiveChild ? 'true' : 'false' }}">
                        <span class="nav-link-icon"><i class="fas fa-folder-open"></i></span>
                        <span class="nav-link-text">{{ $menu->title }}</span>
                        <i class="fas fa-chevron-up nav-chevron"></i>
                    </button>
                    <div class="nav-group-items {{ $hasActiveChild ? '' : 'collapsed' }}"
                         id="{{ $groupId }}"
                         style="max-height: {{ $hasActiveChild ? ($menu->children->count() * 48 + 8).'px' : '0' }}">
                        @foreach($menu->children as $child)
                            @if($child->route_name)
                                <a href="{{ $accessMenu->resolveMenuUrl($child) }}"
                                   class="nav-link-item sub {{ $accessMenu->isMenuActive($child) ? 'active' : '' }}"
                                   title="{{ $child->title }}">
                                    <span class="nav-link-icon"><i class="{{ $child->icon ?: 'fas fa-circle' }}"></i></span>
                                    <span class="nav-link-text">{{ $child->title }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @elseif($menu->route_name)
                <a href="{{ $accessMenu->resolveMenuUrl($menu) }}"
                   class="nav-link-item {{ $accessMenu->isMenuActive($menu) ? 'active' : '' }}"
                   title="{{ $menu->title }}">
                    <span class="nav-link-icon"><i class="{{ $menu->icon ?: 'fas fa-circle' }}"></i></span>
                    <span class="nav-link-text">{{ $menu->title }}</span>
                </a>
            @else
                <div class="nav-group">
                    <button type="button" class="nav-group-toggle collapsed" disabled style="opacity:.6; cursor:default;">
                        <span class="nav-link-icon"><i class="fas fa-layer-group"></i></span>
                        <span class="nav-link-text">{{ $menu->title }}</span>
                    </button>
                </div>
            @endif
        @empty
            <p class="sidebar-empty mb-0">{{ $emptyMessage ?? 'No menus assigned.' }}</p>
        @endforelse
    </nav>
</aside>

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>
