<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pengajuan {{ $layanan->nama }}</title><link rel="stylesheet" href="{{ asset('css/kmu.css') }}"></head>
<body>
<nav class="kmu-nav">
<a class="kmu-brand" href="{{ route('layanan.index') }}"><img src="{{ asset('assets/logo-kota-magelang.jpg') }}" alt="Logo Kota Magelang"><span>Pelayanan Administrasi<br>Kecamatan Magelang Utara</span></a>
<div class="kmu-navlinks"><a href="{{ route('layanan.index') }}">Beranda</a><a class="active">Pengajuan</a><a href="{{ route('dashboard') }}">Dashboard FO</a></div>
</nav>
<main class="form-wrap">
<a class="back" href="{{ route('layanan.show', $layanan) }}">← Kembali ke detail layanan</a>
<div class="form-header"><div class="eyebrow">Pengajuan Surat</div><h1>{{ $layanan->nama }}</h1><p>Lengkapi data warga dan unggah seluruh dokumen yang dipersyaratkan.</p></div>
<div class="steps"><div class="step active">01 · Data Warga</div><div class="step active">02 · Data Surat & Dokumen</div><div class="step active">03 · Generate Surat</div></div>
@if ($errors->any())<div class="error"><strong>Data belum lengkap.</strong><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form action="{{ route('permohonan.store', $layanan) }}" method="POST" enctype="multipart/form-data">
@csrf
<section class="section-box"><h2>1. Data Warga</h2><div class="grid-2">
<div class="form-group"><label>Nama Lengkap <span>*</span></label><input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" required></div>
<div class="form-group"><label>NIK <span>*</span></label><input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" minlength="16" inputmode="numeric" required><div class="hint">16 digit. Data disimpan terenkripsi.</div></div>
<div class="form-group"><label>Tanggal Lahir <span>*</span></label><input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required></div>
<div class="form-group"><label>RT <span>*</span></label><input type="text" name="rt" value="{{ old('rt') }}" maxlength="3" required></div>
<div class="form-group"><label>RW <span>*</span></label><input type="text" name="rw" value="{{ old('rw') }}" maxlength="3" required></div>
</div></section>
@if(count($fields)>0)<section class="section-box"><h2>2. Data Surat</h2><div class="grid-2">
@foreach($fields as $field => $config)
<div class="form-group" @if(($config['type'] ?? 'text')==='textarea') style="grid-column:1/-1" @endif>
<label>{{ $config['label'] }} <span>*</span></label>
@if(($config['type'] ?? 'text')==='select')
<select name="data_surat[{{ $field }}]" required><option value="">-- Pilih --</option>@foreach($config['options'] as $option)<option value="{{ $option }}" @selected(old('data_surat.'.$field)===$option)>{{ $option }}</option>@endforeach</select>
@elseif(($config['type'] ?? 'text')==='textarea')
<textarea name="data_surat[{{ $field }}]" required>{{ old('data_surat.'.$field) }}</textarea>
@else
<input type="{{ ($config['type'] ?? 'text')==='number' ? 'number' : 'text' }}" name="data_surat[{{ $field }}]" value="{{ old('data_surat.'.$field) }}" required>
@endif
</div>
@endforeach
</div></section>@endif
<section class="section-box"><h2>{{ count($fields)>0 ? '3' : '2' }}. Dokumen Persyaratan</h2><div class="required-note">Format dan ukuran file mengikuti aturan masing-masing persyaratan.</div>
@foreach($layanan->persyaratans as $persyaratan)
<div class="form-group upload"><label>{{ $persyaratan->nama }} @if($persyaratan->wajib)<span>*</span>@else <small>(opsional)</small>@endif</label>
<div class="hint">{{ strtoupper(str_replace(',', ', ', $persyaratan->tipe_file)) }} · Maks. {{ $persyaratan->maks_size >= 1024 ? ($persyaratan->maks_size / 1024).' MB' : $persyaratan->maks_size.' KB' }}</div>
<input type="file" name="persyaratan[{{ $persyaratan->id }}]" accept="{{ collect(explode(',', $persyaratan->tipe_file))->map(fn($ext)=>'.'.trim($ext))->implode(',') }}" @if($persyaratan->wajib) required @endif>
</div>
@endforeach
</section>
<div class="actions"><a class="secondary-btn" href="{{ route('layanan.show',$layanan) }}">Batal</a><button class="primary-btn" type="submit">Generate Surat →</button></div>
</form>
</main>
</body>
</html>
