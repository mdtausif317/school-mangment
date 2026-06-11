@extends('layouts.super-admin')

@section('title', 'Menu Management')
@section('page-title', 'Menu Management')

@push('styles')
<style>
    .text-brand { color: #0a5f47; }
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
    .menu-panel { min-height: 500px; }
    .drag-handle { cursor: grab; padding: .25rem .5rem; }
    .drag-handle:active { cursor: grabbing; }
    .menu-row.sortable-ghost { opacity: .45; }
    .menu-row.sortable-chosen .menu-row-inner { border-color: #0a5f47; box-shadow: 0 0 0 2px rgba(10,95,71,.15); }
    .menu-sortable { min-height: 8px; }
    @media (max-width: 768px) { .menu-panel { min-height: auto; } }
</style>
@endpush

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm menu-panel">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-plus-circle me-2 text-brand"></i>Add Menu Item
            </div>
            <div class="card-body">
                <div class="alert alert-light border small mb-3">
                    <strong>Simple guide:</strong>
                    <ul class="mb-0 ps-3">
                        <li><strong>Title</strong> — name shown in sidebar</li>
                        <li><strong>Slug</strong> — unique ID (auto from title)</li>
                        <li><strong>Parent</strong> — optional, to group menus together</li>
                        <li>Page link is set <strong>automatically</strong> — no need to choose route</li>
                        <li>A <code>.blade.php</code> file is created in the folder you choose below</li>
                    </ul>
                </div>
                <form id="create_new_page">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Parent Menu</label>
                        <select name="parent_id" id="menu_parent_id" class="form-select">
                            <option value="">This is a Parent Menu</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" data-scope="{{ $parent->scope }}">{{ $parent->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Menu Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="menu_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" name="slug" id="menu_slug" class="form-control" required>
                            <button type="button" class="btn btn-outline-secondary" id="regenerate_slug" title="Regenerate">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Create page file in folder <span class="text-danger">*</span></label>
                        <select name="page_folder" id="menu_page_folder" class="form-select" required>
                            <option value="super-admin">Super Admin — resources/views/super-admin/</option>
                            <option value="school">School — resources/views/school/</option>
                        </select>
                        <small class="text-muted" id="file_path_preview">File: resources/views/super-admin/your-slug.blade.php</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon</label>
                        <input type="text" name="icon" class="form-control" value="fas fa-circle" placeholder="fas fa-home">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="display_in_menu" class="form-check-input" id="display_in_menu" checked>
                        <label class="form-check-label" for="display_in_menu">Display in Menu</label>
                    </div>
                    <button type="submit" class="btn btn-brand w-100" id="submit_menu">
                        <i class="fas fa-save me-1"></i> Add Menu
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm menu-panel">
            <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="fas fa-sitemap me-2 text-brand"></i>Menu Structure</span>
                <small class="text-muted"><i class="fas fa-grip-vertical me-1"></i>Drag to reorder or move</small>
            </div>
            <div class="card-body">
                @if($menuTree->isEmpty())
                    <p class="text-muted text-center py-4">No menus yet. Add your first menu item.</p>
                @else
                    <div class="menu-sortable" id="menuSortableRoot" data-parent-id="">
                        @include('super-admin.menu-tree', ['menus' => $menuTree, 'parentId' => null])
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Menu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit_menu_form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="menu_id" id="edit_menu_id">
                    <div class="mb-3">
                        <label class="form-label">Parent Menu</label>
                        <select name="parent_id" id="edit_parent_id" class="form-select">
                            <option value="">This is a Parent Menu</option>
                            @foreach($allMenus as $parent)
                                <option value="{{ $parent->id }}" data-scope="{{ $parent->scope }}">{{ $parent->title }} ({{ $parent->slug }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Menu Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="edit_menu_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" name="slug" id="edit_menu_slug" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Page file folder <span class="text-danger">*</span></label>
                        <select name="page_folder" id="edit_page_folder" class="form-select" required>
                            <option value="super-admin">Super Admin — resources/views/super-admin/</option>
                            <option value="school">School — resources/views/school/</option>
                        </select>
                        <small class="text-muted" id="edit_file_path_preview"></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Icon</label>
                        <input type="text" name="icon" id="edit_menu_icon" class="form-control">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="display_in_menu" class="form-check-input" id="edit_display_in_menu">
                        <label class="form-check-label" for="edit_display_in_menu">Display in Menu</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="addButtonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Button — <span id="modal_menu_title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="add_button_form">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="menu_id" id="modal_menu_id">
                    <div class="mb-3">
                        <label class="form-label">Button Title <span class="text-danger">*</span></label>
                        <input type="text" name="button_title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Button Link <span class="text-danger">*</span></label>
                        <input type="text" name="button_link" class="form-control" required placeholder="e.g. student-add">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="button_status" class="form-check-input" id="button_active" checked>
                        <label class="form-check-label" for="button_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-brand">Add Button</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100"></div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function notify(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} shadow`;
    toast.textContent = message;
    document.getElementById('toast-container').appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function slugify(text) {
    const trimmed = (text || '').trim();
    if (trimmed === '#') return '#';

    return trimmed.toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function isGroupMenuSlug(slug) {
    return (slug || '').trim() === '#';
}

function scopeToFolder(scope) {
    return scope === 'school' ? 'school' : 'super-admin';
}

function updateFilePreview(slugInput, folderSelect, previewEl, parentSelect = null) {
    const rawSlug = (slugInput?.value || '').trim();
    const slug = isGroupMenuSlug(rawSlug) ? '#' : (slugify(rawSlug) || 'your-slug');
    let folder = folderSelect?.value || 'super-admin';
    const isParentMenu = !parentSelect?.value;

    if (parentSelect?.value) {
        const parentScope = parentSelect.selectedOptions[0]?.dataset.scope;
        folder = scopeToFolder(parentScope || 'platform');
        if (folderSelect) {
            folderSelect.value = folder;
            folderSelect.disabled = true;
        }
    } else if (folderSelect) {
        folderSelect.disabled = false;
    }

    if (previewEl) {
        if (isGroupMenuSlug(slug)) {
            previewEl.textContent = 'Parent menu only — use # as slug (no page file, can be reused)';
        } else {
            previewEl.textContent = `File: resources/views/${folder}/${slug}.blade.php`;
        }
    }

    if (isParentMenu && !slugManual && slugInput && !rawSlug) {
        slugInput.value = '#';
    }
}

let slugManual = false;
const titleInput = document.getElementById('menu_title');
const slugInput = document.getElementById('menu_slug');
const createParentSelect = document.getElementById('menu_parent_id');
const createFolderSelect = document.getElementById('menu_page_folder');
const filePreview = document.getElementById('file_path_preview');

titleInput?.addEventListener('input', () => {
    if (!slugManual) slugInput.value = slugify(titleInput.value);
    updateFilePreview(slugInput, createFolderSelect, filePreview, createParentSelect);
});
slugInput?.addEventListener('input', () => {
    slugManual = true;
    updateFilePreview(slugInput, createFolderSelect, filePreview, createParentSelect);
});
createFolderSelect?.addEventListener('change', () => {
    updateFilePreview(slugInput, createFolderSelect, filePreview, createParentSelect);
});
createParentSelect?.addEventListener('change', () => {
    if (!createParentSelect.value && !slugManual && slugInput) {
        slugInput.value = '#';
    }
    updateFilePreview(slugInput, createFolderSelect, filePreview, createParentSelect);
});
document.getElementById('regenerate_slug')?.addEventListener('click', () => {
    slugManual = false;
    slugInput.value = slugify(titleInput.value);
    updateFilePreview(slugInput, createFolderSelect, filePreview, createParentSelect);
});
updateFilePreview(slugInput, createFolderSelect, filePreview, createParentSelect);

document.getElementById('create_new_page')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = document.getElementById('submit_menu');
    btn.disabled = true;

    const body = {
        parent_id: form.parent_id.value || null,
        title: form.title.value,
        slug: form.slug.value,
        page_folder: form.page_folder.value,
        icon: form.icon.value,
        display_in_menu: form.display_in_menu.checked ? 0 : 1,
    };

    try {
        const res = await fetch('{{ route('super-admin.menu.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.status === 'success') {
            notify(data.message);
            setTimeout(() => location.reload(), 800);
        } else {
            notify(data.message || 'Error', 'error');
        }
    } catch {
        notify('Request failed', 'error');
    }
    btn.disabled = false;
});

document.querySelectorAll('.menu-display-toggle').forEach(toggle => {
    toggle.addEventListener('change', async function () {
        const menuId = this.dataset.menuId;
        const display = this.checked ? 0 : 1;
        const row = this.closest('.menu-row');

        try {
            const res = await fetch('{{ route('super-admin.menu.display') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ menu_id: parseInt(menuId), display: display === 1 }),
            });
            const data = await res.json();
            if (data.status === 'success') {
                row.classList.toggle('opacity-50', display === 1);
                this.nextElementSibling.textContent = display === 0 ? 'Show' : 'Hide';
                notify(data.message);
            }
        } catch {
            notify('Failed to update visibility', 'error');
            this.checked = !this.checked;
        }
    });
});

const buttonModal = new bootstrap.Modal(document.getElementById('addButtonModal'));
document.querySelectorAll('.add-btn-trigger').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('modal_menu_id').value = btn.dataset.menuId;
        document.getElementById('modal_menu_title').textContent = btn.dataset.menuTitle;
        buttonModal.show();
    });
});

document.getElementById('add_button_form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const body = {
        menu_id: parseInt(form.menu_id.value),
        button_title: form.button_title.value,
        button_link: form.button_link.value,
        button_status: form.button_status.checked ? 0 : 1,
    };

    try {
        const res = await fetch('{{ route('super-admin.menu.button.store') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.status === 'success') {
            notify(data.message);
            buttonModal.hide();
            setTimeout(() => location.reload(), 800);
        } else {
            notify(data.message || 'Error', 'error');
        }
    } catch {
        notify('Request failed', 'error');
    }
});

document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        if (!confirm('Delete this button?')) return;
        try {
            const res = await fetch('{{ route('super-admin.menu.button.destroy') }}', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ button_id: parseInt(btn.dataset.buttonId) }),
            });
            const data = await res.json();
            if (data.status === 'success') {
                notify(data.message);
                setTimeout(() => location.reload(), 800);
            }
        } catch {
            notify('Delete failed', 'error');
        }
    });
});

function collectMenuOrder() {
    const items = [];
    document.querySelectorAll('.menu-sortable').forEach(container => {
        const parentId = container.dataset.parentId ? parseInt(container.dataset.parentId) : null;
        container.querySelectorAll(':scope > .menu-row').forEach((row, index) => {
            items.push({
                id: parseInt(row.dataset.menuId),
                parent_id: parentId,
                sort_order: index + 1,
            });
        });
    });
    return items;
}

let reorderTimer = null;
async function saveMenuOrder() {
    clearTimeout(reorderTimer);
    reorderTimer = setTimeout(async () => {
        try {
            const res = await fetch('{{ route('super-admin.menu.reorder') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ items: collectMenuOrder() }),
            });
            const data = await res.json();
            if (data.status === 'success') {
                notify(data.message);
            } else {
                notify(data.message || 'Reorder failed', 'error');
            }
        } catch {
            notify('Reorder failed', 'error');
        }
    }, 400);
}

document.querySelectorAll('.menu-sortable').forEach(el => {
    new Sortable(el, {
        group: 'menu-tree',
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        fallbackOnBody: true,
        swapThreshold: 0.65,
        onEnd: saveMenuOrder,
    });
});

const editMenuModal = new bootstrap.Modal(document.getElementById('editMenuModal'));
const editParentSelect = document.getElementById('edit_parent_id');
const editFolderSelect = document.getElementById('edit_page_folder');
const editSlugInput = document.getElementById('edit_menu_slug');
const editFilePreview = document.getElementById('edit_file_path_preview');

editParentSelect?.addEventListener('change', () => {
    updateFilePreview(editSlugInput, editFolderSelect, editFilePreview, editParentSelect);
});
editFolderSelect?.addEventListener('change', () => {
    updateFilePreview(editSlugInput, editFolderSelect, editFilePreview, editParentSelect);
});
editSlugInput?.addEventListener('input', () => {
    updateFilePreview(editSlugInput, editFolderSelect, editFilePreview, editParentSelect);
});

document.querySelectorAll('.edit-menu-trigger').forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('.menu-row');
        const menuId = row.dataset.menuId;

        document.getElementById('edit_menu_id').value = menuId;
        document.getElementById('edit_menu_title').value = row.dataset.title;
        editSlugInput.value = row.dataset.slug;
        editFolderSelect.value = scopeToFolder(row.dataset.scope || 'platform');
        editFolderSelect.disabled = false;
        document.getElementById('edit_menu_icon').value = row.dataset.icon;
        document.getElementById('edit_display_in_menu').checked = row.dataset.display === '0';

        [...editParentSelect.options].forEach(opt => {
            opt.hidden = opt.value === menuId;
        });
        editParentSelect.value = row.dataset.parentId || '';

        updateFilePreview(editSlugInput, editFolderSelect, editFilePreview, editParentSelect);
        editMenuModal.show();
    });
});

document.getElementById('edit_menu_form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;

    const body = {
        menu_id: parseInt(form.menu_id.value),
        parent_id: form.parent_id.value || null,
        title: form.title.value,
        slug: form.slug.value,
        page_folder: form.page_folder.value,
        icon: form.icon.value,
        display_in_menu: form.display_in_menu.checked ? 0 : 1,
    };

    try {
        const res = await fetch('{{ route('super-admin.menu.update') }}', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (data.status === 'success') {
            notify(data.message);
            editMenuModal.hide();
            setTimeout(() => location.reload(), 800);
        } else {
            notify(data.message || 'Error', 'error');
        }
    } catch {
        notify('Update failed', 'error');
    }
});
</script>
@endpush
