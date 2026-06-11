@php
    $isChecked = in_array($menu->id, array_map('intval', (array) $selected), true);
    $children = $menu->children ?? collect();
    $hasChildren = $children->isNotEmpty();
@endphp

<div class="access-menu-node {{ $hasChildren ? 'parent' : '' }}">
    <div class="form-check mb-2">
        <input class="form-check-input" type="checkbox"
               name="{{ $inputName }}[]"
               value="{{ $menu->id }}"
               id="menu_{{ $menu->id }}"
               {{ $isChecked ? 'checked' : '' }}>
        <label class="form-check-label" for="menu_{{ $menu->id }}">
            @if($hasChildren)
                <i class="fas fa-folder-open text-muted me-1 small"></i>
            @else
                <i class="fas fa-file text-muted me-1 small"></i>
            @endif
            {{ $menu->title }}
            <code class="small text-muted">{{ $menu->slug }}</code>
        </label>
    </div>

    @if($hasChildren)
        <div class="access-menu-children">
            @foreach($children as $child)
                @include('school.access-list-node', [
                    'menu' => $child,
                    'selected' => $selected,
                    'inputName' => $inputName,
                    'depth' => ($depth ?? 0) + 1,
                ])
            @endforeach
        </div>
    @endif
</div>
