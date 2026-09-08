<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Pengajuan #{{ $permohonan->id }} — Kecamatan Magelang Utara</title>
<link rel="stylesheet" href="{{ asset('css/kmu.css') }}">
</head>
<body>
<nav class="kmu-nav">
<a class="kmu-brand" href="{{ route('layanan.index') }}"><img src="{{ asset('assets/logo-kota-magelang.jpg') }}" alt="Logo Kota Magelang"><span>Pelayanan Administrasi<br>Kecamatan Magelang Utara</span></a>
<div class="kmu-navlinks"><a href="{{ route('layanan.index') }}">Beranda</a><a class="active" href="{{ route('dashboard') }}">Dashboard FO</a><a class="kmu-login" href="{{ route('layanan.index') }}">+ Pengajuan Baru</a><form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button class="nav-logout" type="submit">Keluar</button></form></div>
</nav>

@if(session('success'))<div class="alert success" style="max-width:1100px;margin:20px auto 0">{{ session('success') }}</div>@endif
<main class="detail-wrap dashboard-detail">
<a class="back" href="{{ route('dashboard') }}">← Kembali ke dashboard</a>
<div class="detail-top">
<div><div class="eyebrow">Pengajuan #{{ str_pad($permohonan->id, 4, '0', STR_PAD_LEFT) }}</div><h1>{{ $permohonan->layanan->nama }}</h1><p class="lead">Dibuat {{ $permohonan->created_at->format('d F Y, H:i') }}</p></div>
<span class="status-badge status-{{ $permohonan->status }}">{{ ucfirst($permohonan->status) }}</span>
</div>

<section class="section-box">
<h2>Data Warga</h2>
<div class="info-grid">
<div><span class="info-label">Nama Lengkap</span><strong>{{ $permohonan->nama_lengkap }}</strong></div>
<div><span class="info-label">Tanggal Lahir</span><strong>{{ $permohonan->tanggal_lahir->format('d F Y') }}</strong></div>
<div><span class="info-label">NIK</span><strong>••••••••••••••••</strong><span class="hint">NIK ditampilkan tersamarkan pada dashboard.</span></div>
<div><span class="info-label">RT / RW</span><strong>RT {{ $permohonan->rt }} / RW {{ $permohonan->rw }}</strong></div>
</div>
</section>

<section class="section-box">
<h2>Data Tambahan Surat</h2>
<div class="info-grid">
@forelse($permohonan->data_surat ?? [] as $key => $value)
<div><span class="info-label">{{ ucwords(str_replace('_', ' ', $key)) }}</span><strong>{{ is_array($value) ? implode(', ', $value) : $value }}</strong></div>
@empty
<div class="empty-inline">Tidak ada data tambahan.</div>
@endforelse
</div>
</section>

<section class="section-box">
<h2>Dokumen Persyaratan</h2>
<div class="doc-list">
@forelse($permohonan->dokumenPersyaratans as $dokumen)
<div class="doc-row"><div><strong>{{ $dokumen->persyaratan->nama }}</strong><span>{{ $dokumen->file_original_name }}</span></div><span class="doc-ok">✓ Tersimpan</span></div>
@empty
<div class="empty-inline">Belum ada dokumen yang tersimpan.</div>
@endforelse
</div>
</section>

<div class="actions"><a class="secondary-btn" href="{{ route('dashboard') }}">Kembali</a><a class="primary-btn" href="{{ route('permohonan.preview', $permohonan) }}">Preview & Cetak Surat →</a></div>

<section class="dashboard-panel" style="max-width:1100px;margin:20px auto"><div class="panel-title-row"><div><h2>Workflow Pengajuan</h2><p>Perbarui status setelah proses pelayanan dilakukan.</p></div></div><form method="POST" action="{{ route('dashboard.pengajuan.status', $permohonan) }}" style="display:flex;gap:12px;align-items:end;flex-wrap:wrap">@csrf @method('PATCH')<div><label for="status">Status</label><select id="status" name="status"><option value="diajukan" @selected($permohonan->status==='diajukan')>Diajukan</option><option value="diproses" @selected($permohonan->status==='diproses')>Diproses</option><option value="selesai" @selected($permohonan->status==='selesai')>Selesai</option></select></div><button class="primary-btn" type="submit">Simpan Status</button></form></section>
</main>
</body>
</html>
