@foreach($menus as $menu)
    @php
        $isHidden = $menu->display;
        $parentKey = $parentId ?? '';
    @endphp

    <div class="menu-row {{ $isHidden ? 'opacity-50' : '' }}"
         data-menu-id="{{ $menu->id }}"
         data-title="{{ $menu->title }}"
         data-slug="{{ $menu->slug }}"
         data-icon="{{ $menu->icon }}"
         data-parent-id="{{ $menu->parent_id ?? '' }}"
         data-display="{{ $menu->display ? 1 : 0 }}">

        <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2 bg-white menu-row-inner">
            <div class="d-flex align-items-center gap-2 flex-grow-1 min-w-0">
                <span class="drag-handle text-muted" title="Drag to reorder">
                    <i class="fas fa-grip-vertical"></i>
                </span>
                <i class="{{ $menu->icon }} text-brand"></i>
                <span class="fw-medium text-truncate">{{ $menu->title }}</span>
                <code class="small text-muted">{{ $menu->slug }}</code>
                @if($menu->children->isNotEmpty())
                    <span class="badge bg-secondary">{{ $menu->children->count() }}</span>
                @endif
                @if($isHidden)
                    <span class="badge bg-warning text-dark">Hidden</span>
                @endif
            </div>
            <div class="d-flex align-items-center gap-2 flex-shrink-0">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input menu-display-toggle" type="checkbox"
                           data-menu-id="{{ $menu->id }}" {{ !$isHidden ? 'checked' : '' }}>
                    <label class="form-check-label small show-label">{{ !$isHidden ? 'Show' : 'Hide' }}</label>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary edit-menu-trigger" title="Edit">
                    <i class="fas fa-pen"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-brand add-btn-trigger"
                        data-menu-id="{{ $menu->id }}" data-menu-title="{{ $menu->title }}">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>

        @if($menu->buttons->isNotEmpty())
            <div class="mb-2 ms-4">
                @foreach($menu->buttons as $button)
                    <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-1 bg-light small">
                        <span>
                            <i class="fas fa-hand-pointer me-1 text-muted"></i>
                            {{ $button->button_title }}
                            <code>{{ $button->button_link }}</code>
                            @if($button->status)
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                data-button-id="{{ $button->id }}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        @if($menu->children->isNotEmpty())
            <div class="menu-sortable ms-3 mb-2" data-parent-id="{{ $menu->id }}">
                @include('super-admin.menu.partials.tree-item', [
                    'menus' => $menu->children,
                    'parentId' => $menu->id,
                ])
            </div>
        @endif
    </div>
@endforeach
