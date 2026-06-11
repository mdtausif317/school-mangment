@props(['menus', 'selected' => [], 'inputName' => 'menu_ids', 'helpText' => null])

<div class="border rounded p-3 bg-light access-menu-tree">
    @forelse($menus as $menu)
        @include('school.access-list-node', [
            'menu' => $menu,
            'selected' => $selected,
            'inputName' => $inputName,
            'depth' => 0,
        ])
    @empty
        <p class="text-muted mb-0">No school pages available yet.</p>
    @endforelse
</div>

<p class="text-muted small mt-2 mb-0">
    <i class="fas fa-info-circle me-1"></i>
    {{ $helpText ?? 'Check the pages this user or designation can open in the school portal.' }}
</p>

@once
    @push('styles')
    <style>
        .access-menu-tree .access-menu-children {
            margin-left: 1.25rem;
            padding-left: .75rem;
            border-left: 2px solid #dee2e6;
            margin-bottom: .25rem;
        }
        .access-menu-tree .access-menu-node.parent > .form-check .form-check-label {
            font-weight: 600;
        }
    </style>
    @endpush
@endonce
