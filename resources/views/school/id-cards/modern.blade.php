{{-- Modern wave header with centered overlapping photo --}}
<div class="id-card id-card-modern mx-auto">
    <div class="id-modern-wave">
        <div class="id-modern-wave-bg"></div>
        <div class="id-modern-wave-curve"></div>
    </div>

    <div class="id-modern-top text-center px-3 pt-3">
        @if($school->logoUrl())
            <img src="{{ $school->logoUrl() }}" alt="" class="id-school-logo id-school-logo-light mb-1">
        @endif
        <div class="id-school-name-light">{{ $school->name }}</div>
        <div class="id-card-subtitle-light">{{ $settings->header_title }}</div>
    </div>

    @if($settings->shows('photo'))
        <div class="id-modern-photo-wrap">
            @include('school.id-cards.partials.photo-circle', ['size' => 120, 'ring' => 4])
        </div>
    @endif

    <div class="id-modern-body text-center px-4 {{ $settings->shows('photo') ? 'pt-2' : 'pt-4' }} pb-3">
        <div class="id-student-name">{{ $student->name }}</div>
        @if($settings->shows('class'))
            <div class="id-student-role mb-3">{{ $student->schoolClass?->displayName() ?? 'Student' }}</div>
        @endif

        <div class="id-detail-grid">
            @if($settings->shows('roll_no'))
                <div class="id-detail-chip">
                    <span class="id-detail-chip-label">Roll</span>
                    <span class="id-detail-chip-value">{{ $student->roll_no }}</span>
                </div>
            @endif
            @if($settings->shows('guardian') && $student->guardian_name)
                <div class="id-detail-chip id-detail-chip-wide">
                    <span class="id-detail-chip-label">Guardian</span>
                    <span class="id-detail-chip-value">{{ $student->guardian_name }}</span>
                </div>
            @endif
            @if($settings->shows('phone') && $student->phone)
                <div class="id-detail-chip">
                    <span class="id-detail-chip-label">Phone</span>
                    <span class="id-detail-chip-value">{{ $student->phone }}</span>
                </div>
            @endif
        </div>

        @if($settings->shows('barcode'))
            <div class="barcode-wrap mt-3 pt-2">
                <svg class="student-barcode"></svg>
                <div class="barcode-text">{{ $barcodeValue }}</div>
            </div>
        @endif

        @if($settings->footer_text)
            <div class="id-card-footer mt-2">{{ $settings->footer_text }}</div>
        @endif
    </div>
</div>
