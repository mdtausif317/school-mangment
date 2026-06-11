@extends('layouts.super-admin')

@section('title', 'Menu Management')

@push('styles')
<style>
    .text-brand { color: #0a5f47; }
    .btn-outline-brand { border-color: #0a5f47; color: #0a5f47; }
    .btn-outline-brand:hover { background: #0a5f47; color: #fff; }
    .menu-panel { min-height: 500px; }
    @media (max-width: 768px) { .menu-panel { min-height: auto; } }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center mb-4">
    <a href="{{ route('super-admin.dashboard') }}" class="btn btn-outline-secondary btn-sm me-3">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="mb-0">Menu Management</h4>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm menu-panel">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-plus-circle me-2 text-brand"></i>Add Menu Item
            </div>
            <div class="card-body">
                <form id="create_new_page">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Parent Menu</label>
                        <select name="parent_id" class="form-select">
                            <option value="">This is a Parent Menu</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->title }}</option>
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
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-sitemap me-2 text-brand"></i>Menu Structure
            </div>
            <div class="card-body">
                @if($menuTree->isEmpty())
                    <p class="text-muted text-center py-4">No menus yet. Add your first menu item.</p>
                @else
                    <div class="accordion" id="menuTreeAccordion">
                        @include('super-admin.menu.partials.tree-item', ['menus' => $menuTree, 'depth' => 0])
                    </div>
                @endif
            </div>
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
    return text.toLowerCase().trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

let slugManual = false;
const titleInput = document.getElementById('menu_title');
const slugInput = document.getElementById('menu_slug');

titleInput?.addEventListener('input', () => {
    if (!slugManual) slugInput.value = slugify(titleInput.value);
});
slugInput?.addEventListener('input', () => { slugManual = true; });
document.getElementById('regenerate_slug')?.addEventListener('click', () => {
    slugManual = false;
    slugInput.value = slugify(titleInput.value);
});

document.getElementById('create_new_page')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = document.getElementById('submit_menu');
    btn.disabled = true;

    const body = {
        parent_id: form.parent_id.value || null,
        title: form.title.value,
        slug: form.slug.value,
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
        const row = this.closest('.menu-tree-item');

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
</script>
@endpush
