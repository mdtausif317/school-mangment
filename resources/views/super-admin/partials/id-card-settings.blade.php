@php
    $settings = $settings ?? null;
    $school = $school ?? null;
    $idCards = app(\App\Services\IdCardService::class);
    $show = $settings?->show_fields ?? $idCards->defaultShowFields();
    $selectedTemplate = old('id_card_template', $settings?->template ?? 'classic');
    $customHtmlDefault = old('id_card_custom_html', $settings?->custom_html ?? $idCards->defaultCustomHtml());
    $previewUrl = $previewUrl ?? route('super-admin.id-card.preview');
@endphp

<h6 class="text-muted text-uppercase small mb-3">Student ID Card Design</h6>
<p class="text-muted small">Each school can have its own ID card layout, colors, and visible fields.</p>

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Card Template <span class="text-danger">*</span></label>
        <select name="id_card_template" id="id_card_template" class="form-select @error('id_card_template') is-invalid @enderror" required>
            @foreach(\App\Models\SchoolIdCardSetting::templateOptions() as $value => $label)
                <option value="{{ $value }}" {{ $selectedTemplate === $value ? 'selected' : '' }}>
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
    <div class="col-12" id="id_card_custom_section" style="{{ $selectedTemplate === 'custom' ? '' : 'display:none;' }}">
        <label class="form-label">Custom HTML Template <span class="text-danger">*</span></label>
        <textarea name="id_card_custom_html" id="id_card_custom_html" rows="14"
                  class="form-control font-monospace small @error('id_card_custom_html') is-invalid @enderror"
                  placeholder="Paste your HTML here using placeholders like @{{student_name}}">{{ $customHtmlDefault }}</textarea>
        @error('id_card_custom_html')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <details class="mt-2">
            <summary class="text-muted small" style="cursor:pointer;">Available placeholders</summary>
            <div class="table-responsive mt-2">
                <table class="table table-sm table-bordered small mb-0">
                    <thead class="table-light">
                        <tr><th>Placeholder</th><th>Description</th></tr>
                    </thead>
                    <tbody>
                        @foreach($idCards->placeholderHelp() as $placeholder => $description)
                            <tr>
                                <td><code>{{ $placeholder }}</code></td>
                                <td class="text-muted">{{ $description }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </details>
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
    <div class="col-12">
        <button type="button" id="id_card_preview_btn" class="btn btn-outline-secondary">
            <i class="fas fa-eye me-1"></i> Preview with Sample Data
        </button>
        <span class="text-muted small ms-2">Opens a new tab with dummy student data</span>
    </div>
</div>

@once
    @push('scripts')
    <script>
        (function () {
            const select = document.getElementById('id_card_template');
            const section = document.getElementById('id_card_custom_section');
            const textarea = document.getElementById('id_card_custom_html');
            const previewBtn = document.getElementById('id_card_preview_btn');
            const previewUrl = @json($previewUrl);

            if (!select || !section) return;

            function toggleCustom() {
                const isCustom = select.value === 'custom';
                section.style.display = isCustom ? '' : 'none';
                if (textarea) {
                    textarea.required = isCustom;
                }
            }

            select.addEventListener('change', toggleCustom);
            toggleCustom();

            if (!previewBtn) return;

            const showFields = ['photo', 'roll_no', 'class', 'guardian', 'phone', 'barcode'];
            const inputFields = [
                'id_card_template',
                'id_card_primary_color',
                'id_card_secondary_color',
                'id_card_header_title',
                'id_card_footer_text',
                'id_card_custom_html',
            ];

            function cloneField(source) {
                if (source.type === 'file' && source.files.length) {
                    const input = document.createElement('input');
                    input.type = 'file';
                    input.name = source.name;
                    const dt = new DataTransfer();
                    Array.from(source.files).forEach(file => dt.items.add(file));
                    input.files = dt.files;
                    return input;
                }

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = source.name;
                input.value = source.value;
                return input;
            }

            previewBtn.addEventListener('click', function () {
                const parentForm = previewBtn.closest('form');
                if (!parentForm) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = previewUrl;
                form.target = '_blank';
                form.enctype = 'multipart/form-data';
                form.style.display = 'none';

                const token = document.querySelector('input[name="_token"]');
                if (token) {
                    form.appendChild(cloneField(token));
                }

                inputFields.forEach(function (name) {
                    const el = parentForm.querySelector('[name="' + name + '"]');
                    if (el && el.value !== '') {
                        form.appendChild(cloneField(el));
                    }
                });

                showFields.forEach(function (field) {
                    const cb = parentForm.querySelector('#id_card_show_' + field);
                    if (cb && cb.checked) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'id_card_show_' + field;
                        input.value = '1';
                        form.appendChild(input);
                    }
                });

                const logo = parentForm.querySelector('[name="school_logo"]');
                if (logo && logo.files.length) {
                    form.appendChild(cloneField(logo));
                }

                const schoolId = @json($school?->id);
                if (schoolId) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'school_id';
                    input.value = schoolId;
                    form.appendChild(input);
                }

                const schoolNameInput = document.querySelector('input[name="name"]');
                if (schoolNameInput && schoolNameInput.value) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'school_name';
                    input.value = schoolNameInput.value;
                    form.appendChild(input);
                }

                document.body.appendChild(form);
                form.submit();
                form.remove();
            });
        })();
    </script>
    @endpush
@endonce
