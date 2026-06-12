<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student ID — {{ $student->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --card-primary: {{ $settings->primary_color }};
            --card-secondary: {{ $settings->secondary_color ?? $settings->primary_color }};
        }
        body { background: #e9ecef; padding: 2rem; }
        .id-name { font-weight: 700; color: #1f2937; }
        .id-meta { font-size: .85rem; color: #4b5563; margin-bottom: .15rem; }
        .barcode-wrap svg { max-width: 100%; height: 50px; }
        .barcode-text { font-size: .7rem; color: #6b7280; letter-spacing: .05em; margin-top: .25rem; }
        .no-print { text-align: center; margin-top: 1.5rem; }
        @media print {
            body { background: #fff; padding: 0; }
            .no-print { display: none !important; }
            .id-card { box-shadow: none !important; margin: 0 !important; }
        }
    </style>
    @stack('card-styles')
</head>
<body>
    @if(!empty($isPreview))
        <div class="no-print alert alert-info text-center mx-auto mb-3" style="max-width:340px;">
            <strong>Preview</strong> — sample dummy data (John Doe, Class 10 — A)
        </div>
    @endif

    @include($cardView, ['customHtml' => $customHtml ?? null])

    <div class="no-print">
        <button type="button" class="btn btn-success me-2" onclick="window.print()">Print Card</button>
        <button type="button" class="btn btn-secondary" onclick="window.close()">Close</button>
    </div>

    @if($settings->shows('barcode'))
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jsbarcode/3.11.6/JsBarcode.all.min.js"></script>
        <script>
            document.querySelectorAll('.student-barcode').forEach(function (el) {
                JsBarcode(el, @json($barcodeValue), {
                    format: 'CODE128',
                    width: 1.5,
                    height: 50,
                    displayValue: false,
                    margin: 0,
                });
            });
        </script>
    @endif
</body>
</html>
