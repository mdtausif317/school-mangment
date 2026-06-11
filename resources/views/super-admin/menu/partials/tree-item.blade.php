@foreach($menus as $index => $menu)
    @php
        $isHidden = $menu->display;
        $hasChildren = $menu->children->isNotEmpty();
        $accordionId = 'menuAccordion' . $menu->id;
    @endphp

    <div class="menu-tree-item {{ $isHidden ? 'opacity-50' : '' }}" style="margin-left: {{ $depth * 1.25 }}rem">
        @if($hasChildren)
            <div class="accordion-item border rounded mb-2">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button"
                            data-bs-toggle="collapse" data-bs-target="#{{ $accordionId }}">
                        <i class="{{ $menu->icon }} me-2 text-brand"></i>
                        {{ $menu->title }}
                        <span class="badge bg-secondary ms-2">{{ $menu->children->count() }}</span>
                        @if($isHidden)
                            <span class="badge bg-warning text-dark ms-2">Hidden</span>
                        @endif
                    </button>
                </h2>
                <div id="{{ $accordionId }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}">
                    <div class="accordion-body pt-0">
                        @include('super-admin.menu.partials.tree-item', ['menus' => $menu->children, 'depth' => $depth + 1])
                    </div>
                </div>
            </div>
        @else
            <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2 bg-white">
                <div class="d-flex align-items-center gap-2 flex-grow-1">
                    <i class="{{ $menu->icon }} text-brand"></i>
                    <span class="fw-medium">{{ $menu->title }}</span>
                    <code class="small text-muted">{{ $menu->slug }}</code>
                    @if($isHidden)
                        <span class="badge bg-warning text-dark">Hidden</span>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input menu-display-toggle" type="checkbox"
                               data-menu-id="{{ $menu->id }}" {{ !$isHidden ? 'checked' : '' }}>
                        <label class="form-check-label small show-label">{{ !$isHidden ? 'Show' : 'Hide' }}</label>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-brand add-btn-trigger"
                            data-menu-id="{{ $menu->id }}" data-menu-title="{{ $menu->title }}">
                        <i class="fas fa-plus"></i> Button
                    </button>
                </div>
            </div>

            @if($menu->buttons->isNotEmpty())
                <div class="mb-2" style="margin-left: {{ ($depth + 1) * 1.25 }}rem">
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
        @endif

        @if($hasChildren)
            <div class="d-flex align-items-center justify-content-end gap-3 mb-2 pe-2">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input menu-display-toggle" type="checkbox"
                           data-menu-id="{{ $menu->id }}" {{ !$isHidden ? 'checked' : '' }}>
                    <label class="form-check-label small show-label">{{ !$isHidden ? 'Show' : 'Hide' }}</label>
                </div>
            </div>
        @endif
    </div>
@endforeach
