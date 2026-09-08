<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Layanan extends Model
{
    protected $fillable = [
        'nama',
        'deskripsi',
        'tte',
        'aktif',
    ];

    protected $casts = [
        'tte' => 'boolean',
        'aktif' => 'boolean',
    ];

    public function persyaratans(): HasMany
    {
        return $this->hasMany(Persyaratan::class);
    }

    public function permohonans(): HasMany
    {
        return $this->hasMany(Permohonan::class);
    }

    public function templateSurat(): HasOne
    {
        return $this->hasOne(TemplateSurat::class);
    }
}
