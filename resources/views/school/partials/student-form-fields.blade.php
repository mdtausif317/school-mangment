@php
    $student = $student ?? null;
@endphp

<div class="col-12">
    <label class="form-label">Student Photo</label>
    <div class="d-flex align-items-start gap-3 flex-wrap">
        <div class="student-photo-preview border rounded bg-white d-flex align-items-center justify-content-center overflow-hidden"
             style="width: 200px; height: 250px;">
            @if($student?->photoUrl())
                <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}" class="img-fluid h-100 w-100" style="object-fit: cover;" id="photoPreview">
            @else
                <div class="text-center text-muted small p-2" id="photoPlaceholder">
                    <i class="fas fa-user fa-3x mb-2 d-block"></i>
                    200 × 250 px<br>Standard ID size
                </div>
                <img src="" alt="" class="img-fluid h-100 w-100 d-none" style="object-fit: cover;" id="photoPreview">
            @endif
        </div>
        <div class="flex-grow-1">
            <input type="file" name="photo" id="photoInput" class="form-control @error('photo') is-invalid @enderror"
                   accept="image/jpeg,image/jpg,image/png,image/webp">
            @error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            <p class="text-muted small mt-2 mb-0">
                Passport-style photo. JPG or PNG, max 2 MB. Auto-resized to <strong>200×250 px</strong> for student ID card.
            </p>
        </div>
    </div>
</div>

<div class="col-md-4">
    <label class="form-label">Class <span class="text-danger">*</span></label>
    <select name="class_id" class="form-select @error('class_id') is-invalid @enderror" required
            {{ $classes->isEmpty() ? 'disabled' : '' }}>
        <option value="">Select class</option>
        @foreach($classes as $class)
            <option value="{{ $class->id }}"
                {{ old('class_id', $student?->class_id) == $class->id ? 'selected' : '' }}>
                {{ $class->displayName() }}
            </option>
        @endforeach
    </select>
    @error('class_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-4">
    <label class="form-label">Roll No <span class="text-danger">*</span></label>
    <input type="text" name="roll_no" class="form-control @error('roll_no') is-invalid @enderror"
           value="{{ old('roll_no', $student?->roll_no) }}" required>
    @error('roll_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-4">
    <label class="form-label">Student Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $student?->name) }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-4">
    <label class="form-label">Gender</label>
    <select name="gender" class="form-select">
        <option value="">Select</option>
        <option value="male" {{ old('gender', $student?->gender) === 'male' ? 'selected' : '' }}>Male</option>
        <option value="female" {{ old('gender', $student?->gender) === 'female' ? 'selected' : '' }}>Female</option>
        <option value="other" {{ old('gender', $student?->gender) === 'other' ? 'selected' : '' }}>Other</option>
    </select>
</div>
<div class="col-md-4">
    <label class="form-label">Date of Birth</label>
    <input type="date" name="date_of_birth" class="form-control"
           value="{{ old('date_of_birth', $student?->date_of_birth?->format('Y-m-d')) }}">
</div>
<div class="col-md-4">
    <label class="form-label">Phone</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $student?->phone) }}">
</div>
<div class="col-md-6">
    <label class="form-label">Email @if(old('create_portal_login', $student ? (bool) $student->user_id : true))<span class="text-danger">*</span>@endif</label>
    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email', $student?->email) }}">
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">Required for student portal login.</div>
</div>
<div class="col-md-6">
    <label class="form-label">Guardian Name</label>
    <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $student?->guardian_name) }}">
</div>
<div class="col-12">
    <label class="form-label">Address</label>
    <textarea name="address" class="form-control" rows="2">{{ old('address', $student?->address) }}</textarea>
</div>
<div class="col-12">
    <div class="form-check">
        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
               {{ old('is_active', $student?->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Active</label>
    </div>
</div>
<div class="col-12">
    <div class="card bg-light border-0">
        <div class="card-body py-3">
            <div class="form-check mb-2">
                <input type="checkbox" name="create_portal_login" value="1" class="form-check-input" id="create_portal_login"
                       {{ old('create_portal_login', $student ? (bool) $student->user_id : true) ? 'checked' : '' }}>
                <label class="form-check-label fw-semibold" for="create_portal_login">Create student portal login</label>
            </div>
            <p class="text-muted small mb-2">
                Student can log in at <strong>/student/login</strong>.
                Default password is the roll number unless you set one below.
            </p>
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small mb-1">Portal password (optional)</label>
                    <input type="text" name="portal_password" class="form-control form-control-sm"
                           placeholder="Leave blank to use roll number">
                </div>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        document.getElementById('photoInput')?.addEventListener('change', function (e) {
            const file = e.target.files[0];
            const preview = document.getElementById('photoPreview');
            const placeholder = document.getElementById('photoPlaceholder');
            if (!file || !preview) return;
            const reader = new FileReader();
            reader.onload = function (ev) {
                preview.src = ev.target.result;
                preview.classList.remove('d-none');
                placeholder?.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        });
    </script>
    @endpush
@endonce
