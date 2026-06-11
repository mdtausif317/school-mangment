@props([
    'menus',
    'currentAccess' => [],
    'useDesignationSlugs' => false,
    'designationLabels' => [],
    'designations' => collect(),
])

<div class="table-responsive">
    <table class="table table-bordered align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th>Menu Page</th>
                @if($useDesignationSlugs)
                    @foreach($designationLabels as $slug => $label)
                        <th class="text-center" style="min-width: 90px;">{{ $label }}</th>
                    @endforeach
                @else
                    @foreach($designations as $designation)
                        <th class="text-center" style="min-width: 90px;">{{ $designation->name }}</th>
                    @endforeach
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($menus as $menu)
                @php
                    $menuPrefix = $menu->parent_id ? '— ' : '';
                    $checkedValues = $currentAccess[$menu->id] ?? [];
                @endphp
                <tr>
                    <td>
                        <span class="fw-medium">{{ $menuPrefix }}{{ $menu->title }}</span>
                        <code class="small text-muted ms-1">{{ $menu->slug }}</code>
                    </td>
                    @if($useDesignationSlugs)
                        @foreach($designationLabels as $slug => $label)
                            <td class="text-center">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="menu_access[{{ $menu->id }}][]"
                                       value="{{ $slug }}"
                                       {{ in_array($slug, (array) old("menu_access.{$menu->id}", $checkedValues), true) ? 'checked' : '' }}>
                            </td>
                        @endforeach
                    @else
                        @foreach($designations as $designation)
                            <td class="text-center">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="menu_access[{{ $menu->id }}][]"
                                       value="{{ $designation->id }}"
                                       {{ in_array($designation->id, array_map('intval', (array) old("menu_access.{$menu->id}", $checkedValues)), true) ? 'checked' : '' }}>
                            </td>
                        @endforeach
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">
                        No school menus available. Add global menus from Menu Management first.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="text-muted small mt-2 mb-0">
    <i class="fas fa-info-circle me-1"></i>
    Check which designations can access each page inside the school portal.
</p>
