@push('card-styles')
<style>@media print { @page { size: landscape; margin: 10mm; } }</style>
@endpush

<div class="id-card mx-auto" style="width:480px;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.12);border:2px solid var(--card-primary);">
    <div class="d-flex align-items-stretch">
        @if($settings->shows('photo'))
            <div style="width:140px;background:#f8f9fa;border-right:2px solid var(--card-primary);display:flex;align-items:center;justify-content:center;padding:.5rem;">
                @if($student->photoUrl())
                    <img src="{{ $student->photoUrl() }}" alt="" style="width:120px;height:150px;object-fit:cover;border-radius:6px;">
                @else
                    <span class="text-muted small">No Photo</span>
                @endif
            </div>
        @endif
        <div class="flex-grow-1 p-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fw-bold" style="color:var(--card-primary);font-size:.95rem;">{{ $school->name }}</div>
                    <div class="small text-muted">{{ $settings->header_title }}</div>
                </div>
                @if($school->logoUrl())
                    <img src="{{ $school->logoUrl() }}" alt="" style="height:36px;">
                @endif
            </div>
            <div class="id-name fs-5">{{ $student->name }}</div>
            <div class="row g-1 mt-1">
                @if($settings->shows('roll_no'))
                    <div class="col-6 id-meta"><strong>Roll:</strong> {{ $student->roll_no }}</div>
                @endif
                @if($settings->shows('class'))
                    <div class="col-6 id-meta"><strong>Class:</strong> {{ $student->schoolClass?->displayName() ?? '—' }}</div>
                @endif
                @if($settings->shows('guardian') && $student->guardian_name)
                    <div class="col-12 id-meta"><strong>Guardian:</strong> {{ $student->guardian_name }}</div>
                @endif
                @if($settings->shows('phone') && $student->phone)
                    <div class="col-12 id-meta"><strong>Phone:</strong> {{ $student->phone }}</div>
                @endif
            </div>
            @if($settings->shows('barcode'))
                <div class="barcode-wrap mt-2">
                    <svg class="student-barcode"></svg>
                    <div class="barcode-text">{{ $barcodeValue }}</div>
                </div>
            @endif
            @if($settings->footer_text)
                <div class="small text-muted mt-1">{{ $settings->footer_text }}</div>
            @endif
        </div>
    </div>
</div>
