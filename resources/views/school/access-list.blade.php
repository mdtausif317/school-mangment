@props(['menus', 'selected' => [], 'inputName' => 'menu_ids'])

<div class="border rounded p-3 bg-light">
    @forelse($menus as $menu)
        @php $prefix = $menu->parent_id ? '— ' : ''; @endphp
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox"
                   name="{{ $inputName }}[]"
                   value="{{ $menu->id }}"
                   id="menu_{{ $menu->id }}"
                   {{ in_array($menu->id, array_map('intval', (array) $selected), true) ? 'checked' : '' }}>
            <label class="form-check-label" for="menu_{{ $menu->id }}">
                {{ $prefix }}{{ $menu->title }}
                <code class="small text-muted">{{ $menu->slug }}</code>
            </label>
        </div>
    @empty
        <p class="text-muted mb-0">No school pages available yet.</p>
    @endforelse
</div>

<p class="text-muted small mt-2 mb-0">
    <i class="fas fa-info-circle me-1"></i>
    Check the pages this user or designation can open in the school portal.
</p>
