<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permohonan extends Model
{
    protected $fillable = [
        'layanan_id',
        'nama_lengkap',
        'tanggal_lahir',
        'nik',
        'rt',
        'rw',
        'data_surat',
        'status',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'data_surat' => 'array',
    ];

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function dokumenPersyaratans(): HasMany
    {
        return $this->hasMany(DokumenPersyaratan::class);
    }

}
