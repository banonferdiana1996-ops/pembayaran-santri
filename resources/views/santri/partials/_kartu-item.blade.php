@php
    $institusi = \App\Support\Setting::get('nama_institusi', config('app.name'));
    $logo = \App\Support\Setting::get('logo', '/img/icon-192.png');
@endphp
<div class="kartu-santri">
    <div class="kartu-header">
        <img src="{{ $logo }}" alt="Logo" class="kartu-logo">
        <div>
            <div class="kartu-institusi">{{ $institusi }}</div>
            <div class="kartu-label">KARTU SANTRI</div>
        </div>
    </div>
    <div class="kartu-body">
        <div class="kartu-foto">
            @if ($santri->foto && is_file(public_path($santri->foto)))
                <img src="{{ asset($santri->foto) }}" alt="Foto">
            @else
                <i class="fas fa-user-graduate"></i>
            @endif
        </div>
        <div class="kartu-info">
            <div class="kartu-field">
                <span class="kartu-field-label">NIS</span>
                <span class="kartu-field-value">{{ $santri->nis }}</span>
            </div>
            <div class="kartu-field">
                <span class="kartu-field-label">Nama</span>
                <span class="kartu-field-value">{{ $santri->nama_lengkap }}</span>
            </div>
            <div class="kartu-field">
                <span class="kartu-field-label">Kelas</span>
                <span class="kartu-field-value">{{ $santri->kelas?->nama_kelas ?? '-' }}</span>
            </div>
            <div class="kartu-field">
                <span class="kartu-field-label">TTL</span>
                <span class="kartu-field-value">{{ $santri->tempat_lahir ? $santri->tempat_lahir.', ' : '' }}{{ $santri->tanggal_lahir?->translatedFormat('d M Y') }}</span>
            </div>
        </div>
        <div class="kartu-qr">
            <img src="{{ $santri->qr }}" alt="QR {{ $santri->nis }}">
            <div class="kartu-qr-nis">{{ $santri->nis }}</div>
        </div>
    </div>
    <div class="kartu-footer">
        <div class="kartu-ttd">
            <span>{{ $institusi }}, {{ now()->translatedFormat('Y') }}</span>
        </div>
    </div>
</div>
