@extends('layouts.student')

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-user me-2 text-brand"></i> My Profile
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-4 text-center">
                        <div class="mx-auto rounded overflow-hidden border mb-3"
                             style="width:200px;height:250px;background:#f8f9fa;">
                            @if($student->photoUrl())
                                <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}"
                                     class="w-100 h-100" style="object-fit:cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                    <i class="fas fa-user fa-4x"></i>
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('student.id-card') }}" class="btn btn-sm btn-brand" target="_blank">
                            <i class="fas fa-id-card me-1"></i> View ID Card
                        </a>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-borderless mb-0">
                            <tbody>
                                <tr><th class="text-muted ps-0" style="width:140px;">Name</th><td>{{ $student->name }}</td></tr>
                                <tr><th class="text-muted ps-0">Roll No</th><td>{{ $student->roll_no }}</td></tr>
                                <tr><th class="text-muted ps-0">Class</th><td>{{ $student->schoolClass?->displayName() ?? '—' }}</td></tr>
                                <tr><th class="text-muted ps-0">Email</th><td>{{ $student->email ?? '—' }}</td></tr>
                                <tr><th class="text-muted ps-0">Phone</th><td>{{ $student->phone ?? '—' }}</td></tr>
                                <tr><th class="text-muted ps-0">Gender</th><td>{{ ucfirst($student->gender ?? '—') }}</td></tr>
                                <tr><th class="text-muted ps-0">Date of Birth</th><td>{{ $student->date_of_birth?->format('d M Y') ?? '—' }}</td></tr>
                                <tr><th class="text-muted ps-0">Guardian</th><td>{{ $student->guardian_name ?? '—' }}</td></tr>
                                <tr><th class="text-muted ps-0">Address</th><td>{{ $student->address ?? '—' }}</td></tr>
                                <tr><th class="text-muted ps-0">School</th><td>{{ $student->school->name }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
