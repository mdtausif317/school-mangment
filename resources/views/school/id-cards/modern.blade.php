<div class="id-card mx-auto d-flex" style="width:380px;min-height:220px;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.12);">
    <div style="width:110px;background:linear-gradient(180deg,var(--card-primary),var(--card-secondary));color:#fff;padding:1rem .75rem;text-align:center;">
        @if($school->logoUrl())
            <img src="{{ $school->logoUrl() }}" alt="" style="height:40px;margin-bottom:.5rem;">
        @endif
        <div style="font-size:.65rem;font-weight:600;line-height:1.3;">{{ $school->name }}</div>
    </div>
    <div class="flex-grow-1 p-3">
        <div class="small fw-semibold mb-2" style="color:var(--card-primary);">{{ $settings->header_title }}</div>
        <div class="d-flex gap-3">
            @if($settings->shows('photo'))
                <div style="width:80px;height:100px;border:2px solid var(--card-primary);border-radius:6px;overflow:hidden;flex-shrink:0;background:#f8f9fa;">
                    @if($student->photoUrl())
                        <img src="{{ $student->photoUrl() }}" alt="" style="width:100%;height:100%;object-fit:cover;">
                    @endif
                </div>
            @endif
            <div class="flex-grow-1">
                <div class="id-name mb-1">{{ $student->name }}</div>
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
            </div>
        </div>
        @if($settings->shows('barcode'))
            <div class="barcode-wrap mt-2 pt-2" style="border-top:1px dashed #dee2e6;">
                <svg class="student-barcode"></svg>
            </div>
        @endif
        @if($settings->footer_text)
            <div class="small text-muted mt-1">{{ $settings->footer_text }}</div>
        @endif
    </div>
</div>
