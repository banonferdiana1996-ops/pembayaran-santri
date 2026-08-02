<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Kartu Santri ({{ $santris->count() }})</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Roboto, Arial, sans-serif; background: #e2e8f0; padding: 16px; }
        .toolbar { text-align: center; margin-bottom: 16px; }
        .toolbar button {
            background: #2563eb; color: #fff; border: 0; border-radius: 8px;
            padding: 10px 22px; font-size: 14px; cursor: pointer;
        }
        .grid { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; }
        .kartu-santri {
            width: 88mm; background: #fff; border-radius: 10px; overflow: hidden;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .18); border: 1px solid #dbe3ef;
            page-break-inside: avoid;
        }
        .kartu-header {
            display: flex; align-items: center; gap: 10px;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            color: #fff; padding: 10px 14px;
        }
        .kartu-logo { width: 38px; height: 38px; object-fit: cover; border-radius: 8px; background: #fff; padding: 2px; }
        .kartu-institusi { font-size: 14px; font-weight: 800; line-height: 1.1; }
        .kartu-label { font-size: 10px; letter-spacing: 3px; opacity: .85; font-weight: 600; margin-top: 2px; }
        .kartu-body { display: flex; align-items: stretch; gap: 12px; padding: 14px; }
        .kartu-foto {
            width: 68px; height: 82px; flex-shrink: 0; border-radius: 6px; overflow: hidden;
            background: #eef2f8; display: flex; align-items: center; justify-content: center; border: 1px solid #dbe3ef;
        }
        .kartu-foto img { width: 100%; height: 100%; object-fit: cover; }
        .kartu-foto i { font-size: 28px; color: #94a3b8; }
        .kartu-info { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 5px; }
        .kartu-field { display: flex; flex-direction: column; }
        .kartu-field-label { font-size: 8px; text-transform: uppercase; color: #94a3b8; letter-spacing: .5px; }
        .kartu-field-value { font-size: 12px; font-weight: 700; color: #1e293b; line-height: 1.15; }
        .kartu-qr { flex-shrink: 0; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; }
        .kartu-qr img { width: 58px; height: 58px; }
        .kartu-qr-nis { font-size: 8px; font-weight: 700; color: #475569; letter-spacing: .5px; }
        .kartu-footer { border-top: 1px dashed #dbe3ef; padding: 8px 14px; }
        .kartu-ttd { font-size: 9px; color: #64748b; text-align: right; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .kartu-santri { box-shadow: none; break-inside: avoid; }
            @page { margin: 5mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()"><i class="fas fa-print"></i> Cetak Semua ({{ $santris->count() }})</button>
    </div>
    <div class="grid">
        @forelse ($santris as $santri)
            @include('santri.partials._kartu-item', ['santri' => $santri])
        @empty
            <div class="toolbar">Tidak ada data santri terpilih.</div>
        @endforelse
    </div>
</body>
</html>
