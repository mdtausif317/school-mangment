<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID — {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #e9ecef; padding: 2rem; }
        .id-card {
            width: 340px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,.12);
            border: 2px solid #0a5f47;
        }
        .id-header {
            background: linear-gradient(135deg, #0a5f47, #0d7a5c);
            color: #fff;
            text-align: center;
            padding: .75rem 1rem;
        }
        .id-header h6 { margin: 0; font-weight: 700; font-size: .95rem; }
        .id-header small { opacity: .9; }
        .id-photo {
            width: 200px;
            height: 250px;
            margin: 1rem auto;
            border: 3px solid #0a5f47;
            border-radius: 8px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .id-photo img { width: 100%; height: 100%; object-fit: cover; }
        .id-body { padding: 0 1.25rem 1rem; text-align: center; }
        .id-name { font-size: 1.15rem; font-weight: 700; color: #1f2937; margin-bottom: .25rem; }
        .id-meta { font-size: .85rem; color: #6b7280; margin-bottom: .15rem; }
        .barcode-wrap { margin-top: .75rem; padding-top: .75rem; border-top: 1px dashed #dee2e6; }
        .barcode-wrap svg { max-width: 100%; height: 50px; }
        .barcode-text { font-size: .7rem; color: #6b7280; letter-spacing: .05em; margin-top: .25rem; }
        .no-print { text-align: center; margin-top: 1.5rem; }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .id-card { box-shadow: none; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="id-card">
        <div class="id-header">
            <h6>{{ $school->name }}</h6>
            <small>Student Identity Card</small>
        </div>
        <div class="id-body">
            <div class="id-photo">
                @if($student->photoUrl())
                    <img src="{{ $student->photoUrl() }}" alt="{{ $student->name }}">
                @else
                    <span class="text-muted small">No Photo</span>
                @endif
            </div>
            <div class="id-name">{{ $student->name }}</div>
            <div class="id-meta"><strong>Roll:</strong> {{ $student->roll_no }}</div>
            <div class="id-meta"><strong>Class:</strong> {{ $student->schoolClass?->displayName() ?? '—' }}</div>
            @if($student->guardian_name)
                <div class="id-meta"><strong>Guardian:</strong> {{ $student->guardian_name }}</div>
            @endif
            @if($student->phone)
                <div class="id-meta"><strong>Phone:</strong> {{ $student->phone }}</div>
            @endif
            <div class="barcode-wrap">
                <svg id="studentBarcode"></svg>
                <div class="barcode-text">{{ $barcodeValue }}</div>
            </div>
        </div>
    </div>

    <div class="no-print">
        <button type="button" class="btn btn-success me-2" onclick="window.print()">
            <i class="fas fa-print me-1"></i> Print Card
        </button>
        <button type="button" class="btn btn-secondary" onclick="window.close()">Close</button>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
    <script>
        JsBarcode('#studentBarcode', @json($barcodeValue), {
            format: 'CODE128',
            width: 1.5,
            height: 50,
            displayValue: false,
            margin: 0,
        });
    </script>
</body>
</html>
