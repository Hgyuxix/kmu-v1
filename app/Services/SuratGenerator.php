<?php

namespace App\Services;

use App\Models\Permohonan;
use Carbon\Carbon;

class SuratGenerator
{
    public function generate(Permohonan $permohonan): string
    {
        $permohonan->load([
            'layanan.templateSurat',
        ]);

        $template = $permohonan->layanan->templateSurat;

        if (!$template) {
            throw new \RuntimeException(
                'Template surat untuk layanan ini belum tersedia.'
            );
        }

        $data = $permohonan->data_surat ?? [];

        $nomorSurat = '470/' . str_pad(
            $permohonan->id,
            4,
            '0',
            STR_PAD_LEFT
        ) . '/KMU';

        $nikEncryption = app(
            NikEncryptionService::class
        );

        $nik = $nikEncryption->decrypt(
            $permohonan->nik
        );

        Carbon::setLocale('id');

        // ==========================================
        // DATA PENANDATANGAN
        // ==========================================

        $namaPejabat = config(
            'penandatangan.nama'
        );

        $jabatanPejabat = config(
            'penandatangan.jabatan'
        );

        // ==========================================
        // DATA SURAT
        // ==========================================

        $replacements = [

            '{{ nomor_surat }}' => $nomorSurat,

            '{{ nama_lengkap }}' => e(
                $permohonan->nama_lengkap
            ),

            '{{ nik }}' => e(
                $nik
            ),

            '{{ tanggal_lahir }}' => $permohonan
                ->tanggal_lahir
                ->translatedFormat('d F Y'),

            '{{ rt }}' => e(
                $permohonan->rt
            ),

            '{{ rw }}' => e(
                $permohonan->rw
            ),

            '{{ nama_layanan }}' => e(
                $permohonan->layanan->nama
            ),

            '{{ tanggal }}' => now()
                ->translatedFormat('d F Y'),

            // PENANDATANGAN OTOMATIS
            '{{ nama_pejabat }}' => e(
                config('penandatangan.nama')
            ),

            '{{ jabatan_pejabat }}' => e(
                config('penandatangan.jabatan')
            ),

            // QR DUMMY
            '{{ qr_code }}' => 'QR-DUMMY-' . $permohonan->id,
        ];

        // ==========================================
        // DATA TAMBAHAN PER LAYANAN
        // ==========================================

        foreach ($data as $key => $value) {

            $replacements[
                '{{ ' . $key . ' }}'
            ] = e($value);

        }

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template->isi_template
        );
    }
}
