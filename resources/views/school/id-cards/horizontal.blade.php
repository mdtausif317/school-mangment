@push('card-styles')
<style>@media print { @page { size: landscape; margin: 10mm; } }</style>
@endpush

{{-- Professional landscape card --}}
<div class="id-card id-card-horizontal mx-auto">
    <div class="id-hz-accent id-hz-accent-left"></div>
    <div class="id-hz-accent id-hz-accent-right"></div>

    <div class="id-hz-inner d-flex align-items-center">
        @if($settings->shows('photo'))
            <div class="id-hz-photo-col text-center">
                @include('school.id-cards.partials.photo-circle', ['size' => 110, 'ring' => 4])
            </div>
        @endif

        <div class="id-hz-content flex-grow-1">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="id-school-name id-school-name-sm">{{ $school->name }}</div>
                    <div class="id-card-subtitle">{{ $settings->header_title }}</div>
                </div>
                @if($school->logoUrl())
                    <img src="{{ $school->logoUrl() }}" alt="" class="id-school-logo">
                @endif
            </div>

            <div class="id-student-name id-student-name-sm mb-2">{{ $student->name }}</div>

            <div class="id-hz-details">
                @if($settings->shows('roll_no'))
                    <div class="id-hz-detail"><strong>Roll</strong> {{ $student->roll_no }}</div>
                @endif
                @if($settings->shows('class'))
                    <div class="id-hz-detail"><strong>Class</strong> {{ $student->schoolClass?->displayName() ?? '—' }}</div>
                @endif
                @if($settings->shows('guardian') && $student->guardian_name)
                    <div class="id-hz-detail"><strong>Guardian</strong> {{ $student->guardian_name }}</div>
                @endif
                @if($settings->shows('phone') && $student->phone)
                    <div class="id-hz-detail"><strong>Phone</strong> {{ $student->phone }}</div>
                @endif
            </div>

            @if($settings->shows('barcode'))
                <div class="barcode-wrap mt-2">
                    <svg class="student-barcode"></svg>
                    <div class="barcode-text">{{ $barcodeValue }}</div>
                </div>
            @endif
        </div>
    </div>

    @if($settings->footer_text)
        <div class="id-hz-footer">{{ $settings->footer_text }}</div>
    @endif
</div>
