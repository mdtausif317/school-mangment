@php
    $settings = $settings ?? null;
    $show = $settings?->show_fields ?? app(\App\Services\IdCardService::class)->defaultShowFields();
@endphp

<h6 class="text-muted text-uppercase small mb-3">Student ID Card Design</h6>
<p class="text-muted small">Each school can have its own ID card layout, colors, and visible fields.</p>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Card Template <span class="text-danger">*</span></label>
        <select name="id_card_template" class="form-select @error('id_card_template') is-invalid @enderror" required>
            @foreach(\App\Models\SchoolIdCardSetting::templateOptions() as $value => $label)
                <option value="{{ $value }}" {{ old('id_card_template', $settings?->template ?? 'classic') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('id_card_template')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label class="form-label">School Logo (for card)</label>
        <input type="file" name="school_logo" class="form-control @error('school_logo') is-invalid @enderror"
               accept="image/jpeg,image/jpg,image/png,image/webp">
        @error('school_logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($school?->logoUrl())
            <div class="mt-2">
                <img src="{{ $school->logoUrl() }}" alt="Logo" class="rounded border" style="height:48px;">
            </div>
        @endif
    </div>
    <div class="col-md-4">
        <label class="form-label">Primary Color <span class="text-danger">*</span></label>
        <input type="color" name="id_card_primary_color" class="form-control form-control-color w-100"
               value="{{ old('id_card_primary_color', $settings?->primary_color ?? '#0a5f47') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Secondary Color</label>
        <input type="color" name="id_card_secondary_color" class="form-control form-control-color w-100"
               value="{{ old('id_card_secondary_color', $settings?->secondary_color ?? '#0d7a5c') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Card Title <span class="text-danger">*</span></label>
        <input type="text" name="id_card_header_title" class="form-control"
               value="{{ old('id_card_header_title', $settings?->header_title ?? 'Student Identity Card') }}" required>
    </div>
    <div class="col-12">
        <label class="form-label">Footer Text (optional)</label>
        <input type="text" name="id_card_footer_text" class="form-control"
               value="{{ old('id_card_footer_text', $settings?->footer_text) }}" placeholder="e.g. Valid for current session">
    </div>
    <div class="col-12">
        <label class="form-label d-block mb-2">Show on ID Card</label>
        <div class="d-flex flex-wrap gap-3">
            @foreach([
                'photo' => 'Photo',
                'roll_no' => 'Roll No',
                'class' => 'Class',
                'guardian' => 'Guardian',
                'phone' => 'Phone',
                'barcode' => 'Barcode',
            ] as $field => $label)
                <div class="form-check">
                    <input type="checkbox" name="id_card_show_{{ $field }}" value="1" class="form-check-input"
                           id="id_card_show_{{ $field }}"
                           {{ old('id_card_show_'.$field, $show[$field] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="id_card_show_{{ $field }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
    </div>
</div>
