<div class="id-card mx-auto" style="width:340px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.12);border:2px solid var(--card-primary);">
    <div style="background:linear-gradient(135deg,var(--card-primary),var(--card-secondary));color:#fff;text-align:center;padding:.75rem 1rem;">
        @if($school->logoUrl())
            <img src="{{ $school->logoUrl() }}" alt="" style="height:36px;margin-bottom:.35rem;">
        @endif
        <h6 style="margin:0;font-weight:700;font-size:.95rem;">{{ $school->name }}</h6>
        <small style="opacity:.9;">{{ $settings->header_title }}</small>
    </div>
    <div class="text-center p-3">
        @if($settings->shows('photo'))
            <div class="mx-auto mb-3" style="width:200px;height:250px;border:3px solid var(--card-primary);border-radius:8px;overflow:hidden;background:#f8f9fa;display:flex;align-items:center;justify-content:center;">
                @if($student->photoUrl())
                    <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}" style="width:100%;height:100%;object-fit:cover;">
                @else
                    <span class="text-muted small">No Photo</span>
                @endif
            </div>
        @endif
        <div class="id-name fs-5 mb-1">{{ $student->name }}</div>
        @if($settings->shows('roll_no'))
            <div class="id-meta"><strong>Roll:</strong> {{ $student->roll_no }}</div>
        @endif
        @if($settings->shows('class'))
            <div class="id-meta"><strong>Class:</strong> {{ $student->schoolClass?->displayName() ?? '—' }}</div>
        @endif
        @if($settings->shows('guardian') && $student->guardian_name)
            <div class="id-meta"><strong>Guardian:</strong> {{ $student->guardian_name }}</div>
        @endif
        @if($settings->shows('phone') && $student->phone)
            <div class="id-meta"><strong>Phone:</strong> {{ $student->phone }}</div>
        @endif
        @if($settings->shows('barcode'))
            <div class="barcode-wrap mt-3 pt-3" style="border-top:1px dashed #dee2e6;">
                <svg class="student-barcode"></svg>
                <div class="barcode-text">{{ $barcodeValue }}</div>
            </div>
        @endif
        @if($settings->footer_text)
            <div class="small text-muted mt-2">{{ $settings->footer_text }}</div>
        @endif
    </div>
</div>
