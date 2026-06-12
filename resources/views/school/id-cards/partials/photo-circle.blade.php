@php
    $size = $size ?? 128;
    $ring = $ring ?? 5;
@endphp
<div class="id-photo-ring mx-auto" style="--photo-size: {{ $size }}px; --ring-width: {{ $ring }}px;">
    <div class="id-photo-inner">
        @if($student->photoUrl())
            <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}">
        @else
            <span class="id-photo-placeholder">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </span>
        @endif
    </div>
</div>
