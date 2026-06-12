{{-- Premium portrait — centered circular photo --}}
<div class="id-card id-card-premium mx-auto">
    <div class="id-corner id-corner-tl"></div>
    <div class="id-corner id-corner-tr"></div>
    <div class="id-corner id-corner-bl"></div>
    <div class="id-corner id-corner-br"></div>
    <div class="id-dots id-dots-left"></div>
    <div class="id-dots id-dots-right"></div>

    <div class="id-card-body text-center">
        <div class="id-card-header pt-4 pb-2 px-3">
            @if($school->logoUrl())
                <img src="{{ $school->logoUrl() }}" alt="" class="id-school-logo mb-2">
            @endif
            <div class="id-school-name">{{ $school->name }}</div>
            <div class="id-card-subtitle">{{ $settings->header_title }}</div>
        </div>

        @if($settings->shows('photo'))
            <div class="id-photo-section my-3">
                @include('school.id-cards.partials.photo-circle', ['size' => 132, 'ring' => 5])
            </div>
        @endif

        <div class="id-student-name px-3">{{ $student->name }}</div>
        @if($settings->shows('class'))
            <div class="id-student-role px-3">{{ $student->schoolClass?->displayName() ?? 'Student' }}</div>
        @endif

        <div class="id-detail-list text-start mx-auto px-4 mt-3 mb-2">
            @if($settings->shows('roll_no'))
                <div class="id-detail-row">
                    <span class="id-detail-label">Roll No</span>
                    <span class="id-detail-value">{{ $student->roll_no }}</span>
                </div>
            @endif
            @if($settings->shows('guardian') && $student->guardian_name)
                <div class="id-detail-row">
                    <span class="id-detail-label">Guardian</span>
                    <span class="id-detail-value">{{ $student->guardian_name }}</span>
                </div>
            @endif
            @if($settings->shows('phone') && $student->phone)
                <div class="id-detail-row">
                    <span class="id-detail-label">Phone</span>
                    <span class="id-detail-value">{{ $student->phone }}</span>
                </div>
            @endif
        </div>

        @if($settings->shows('barcode'))
            <div class="barcode-wrap px-4 pt-2 pb-1">
                <svg class="student-barcode"></svg>
                <div class="barcode-text">{{ $barcodeValue }}</div>
            </div>
        @endif

        @if($settings->footer_text)
            <div class="id-card-footer">{{ $settings->footer_text }}</div>
        @endif
    </div>
</div>
