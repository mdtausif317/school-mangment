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
            --card-primary-soft: color-mix(in srgb, var(--card-primary) 12%, white);
        }

        body {
            background: linear-gradient(145deg, #e8edf2 0%, #f4f6f8 100%);
            padding: 2rem;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .id-card {
            position: relative;
            background: #fff;
            box-shadow: 0 12px 40px rgba(15, 23, 42, .14), 0 2px 8px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        /* ── Premium portrait ── */
        .id-card-premium {
            width: 360px;
            min-height: 540px;
            border-radius: 16px;
        }

        .id-card-body { position: relative; z-index: 2; }

        .id-corner {
            position: absolute;
            width: 90px;
            height: 90px;
            z-index: 1;
            opacity: .95;
        }
        .id-corner-tl {
            top: 0; left: 0;
            background: linear-gradient(135deg, var(--card-primary) 50%, transparent 50%);
            clip-path: polygon(0 0, 100% 0, 0 100%);
        }
        .id-corner-tr {
            top: 0; right: 0;
            background: linear-gradient(225deg, var(--card-secondary) 45%, transparent 45%);
            clip-path: polygon(100% 0, 100% 100%, 0 0);
            opacity: .35;
        }
        .id-corner-bl {
            bottom: 0; left: 0;
            background: linear-gradient(45deg, var(--card-secondary) 40%, transparent 40%);
            clip-path: polygon(0 100%, 100% 100%, 0 0);
            opacity: .25;
        }
        .id-corner-br {
            bottom: 0; right: 0;
            background: linear-gradient(315deg, var(--card-primary) 55%, transparent 55%);
            clip-path: polygon(100% 100%, 0 100%, 100% 0);
        }

        .id-dots {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 120px;
            background: repeating-linear-gradient(
                to bottom,
                var(--card-primary-soft) 0,
                var(--card-primary-soft) 4px,
                transparent 4px,
                transparent 10px
            );
            opacity: .6;
            z-index: 1;
        }
        .id-dots-left { left: 14px; }
        .id-dots-right { right: 14px; }

        .id-school-logo { height: 42px; object-fit: contain; }
        .id-school-logo-light { filter: brightness(0) invert(1); opacity: .95; height: 36px; }

        .id-school-name {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -.02em;
            line-height: 1.25;
        }
        .id-school-name-sm { font-size: .92rem; }
        .id-school-name-light {
            font-size: .95rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 1px 2px rgba(0,0,0,.15);
        }

        .id-card-subtitle {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--card-primary);
            margin-top: .2rem;
        }
        .id-card-subtitle-light {
            font-size: .68rem;
            font-weight: 500;
            color: rgba(255,255,255,.88);
            letter-spacing: .06em;
        }

        /* Circular photo */
        .id-photo-ring {
            width: calc(var(--photo-size) + var(--ring-width) * 2 + 8px);
            height: calc(var(--photo-size) + var(--ring-width) * 2 + 8px);
            border-radius: 50%;
            padding: calc(var(--ring-width) + 4px);
            background: linear-gradient(145deg, var(--card-primary), var(--card-secondary));
            box-shadow: 0 8px 24px color-mix(in srgb, var(--card-primary) 35%, transparent);
        }
        .id-photo-inner {
            width: var(--photo-size);
            height: var(--photo-size);
            border-radius: 50%;
            overflow: hidden;
            background: #f1f5f9;
            border: 3px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .id-photo-inner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .id-photo-placeholder {
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .id-student-name {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -.02em;
            line-height: 1.2;
        }
        .id-student-name-sm { font-size: 1.15rem; }

        .id-student-role {
            font-size: .82rem;
            font-weight: 600;
            color: var(--card-primary);
            margin-top: .15rem;
        }

        .id-detail-list { max-width: 280px; }

        .id-detail-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: .45rem 0;
            border-bottom: 1px dashed #e2e8f0;
            font-size: .82rem;
        }
        .id-detail-row:last-child { border-bottom: none; }
        .id-detail-label {
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            font-size: .68rem;
            letter-spacing: .04em;
        }
        .id-detail-value {
            font-weight: 600;
            color: #1e293b;
            text-align: right;
            max-width: 58%;
            word-break: break-word;
        }

        .id-card-footer {
            background: linear-gradient(90deg, var(--card-primary), var(--card-secondary));
            color: #fff;
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-align: center;
            padding: .55rem 1rem;
            margin-top: .5rem;
        }

        /* ── Modern wave ── */
        .id-card-modern {
            width: 360px;
            border-radius: 16px;
            padding-bottom: .25rem;
            position: relative;
        }

        .id-modern-wave { position: relative; height: 130px; }
        .id-modern-wave-bg {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--card-primary) 0%, var(--card-secondary) 100%);
        }
        .id-modern-wave-curve {
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 40px;
            background: #fff;
            border-radius: 50% 50% 0 0 / 100% 100% 0 0;
        }

        .id-modern-top {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 2;
        }

        .id-modern-photo-wrap {
            position: relative;
            z-index: 3;
            margin-top: -20px;
            text-align: center;
        }

        .id-detail-grid {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            justify-content: center;
        }
        .id-detail-chip {
            background: var(--card-primary-soft);
            border-radius: 8px;
            padding: .4rem .65rem;
            min-width: 46%;
            text-align: left;
        }
        .id-detail-chip-wide { min-width: 100%; }
        .id-detail-chip-label {
            display: block;
            font-size: .62rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--card-primary);
        }
        .id-detail-chip-value {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #1e293b;
        }

        /* ── Horizontal ── */
        .id-card-horizontal {
            width: 520px;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
        }

        .id-hz-accent {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 6px;
            background: linear-gradient(180deg, var(--card-primary), var(--card-secondary));
        }
        .id-hz-accent-left { left: 0; }
        .id-hz-accent-right { right: 0; opacity: .4; }

        .id-hz-inner { padding: 1.25rem 1.5rem; gap: 1.25rem; }
        .id-hz-photo-col { flex-shrink: 0; padding-left: .5rem; }

        .id-hz-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .35rem .75rem;
        }
        .id-hz-detail {
            font-size: .8rem;
            color: #475569;
        }
        .id-hz-detail strong {
            display: block;
            font-size: .62rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #94a3b8;
            font-weight: 700;
        }

        .id-hz-footer {
            background: var(--card-primary-soft);
            color: var(--card-primary);
            font-size: .72rem;
            font-weight: 600;
            text-align: center;
            padding: .45rem;
            border-top: 1px solid #e2e8f0;
        }

        /* ── Shared ── */
        .barcode-wrap svg { max-width: 100%; height: 44px; }
        .barcode-text {
            font-size: .68rem;
            color: #94a3b8;
            letter-spacing: .06em;
            margin-top: .2rem;
        }

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
        <div class="no-print alert alert-info text-center mx-auto mb-3" style="max-width:360px;">
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
                    height: 44,
                    displayValue: false,
                    margin: 0,
                });
            });
        </script>
    @endif
</body>
</html>
