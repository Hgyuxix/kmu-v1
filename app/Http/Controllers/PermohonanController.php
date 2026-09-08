<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Permohonan;
use App\Services\NikEncryptionService;
use App\Services\SuratGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PermohonanController extends Controller
{
    public function create(Layanan $layanan)
    {
        abort_if(!$layanan->aktif, 404);

        $layanan->load('persyaratans');

        $fields = config('surat.' . $layanan->id, []);

        return view('permohonan.create', compact(
            'layanan',
            'fields'
        ));
    }

    public function store(
        Request $request,
        Layanan $layanan,
        NikEncryptionService $nikEncryptionService
    ) {
        abort_if(!$layanan->aktif, 404);

        $layanan->load('persyaratans');

        $fields = config('surat.' . $layanan->id, []);

        /*
        |--------------------------------------------------------------------------
        | Validasi data utama warga
        |--------------------------------------------------------------------------
        */

        $rules = [
            'nama_lengkap' => [
                'required',
                'string',
                'max:255',
            ],

            'tanggal_lahir' => [
                'required',
                'date',
            ],

            'nik' => [
                'required',
                'digits:16',
            ],

            'rt' => [
                'required',
                'string',
                'max:3',
            ],

            'rw' => [
                'required',
                'string',
                'max:3',
            ],
        ];

        /*
        |--------------------------------------------------------------------------
        | Validasi field tambahan surat
        |--------------------------------------------------------------------------
        */

        foreach ($fields as $field => $label) {
            $rules['data_surat.' . $field] = [
                'required',
                'string',
                'max:1000',
            ];
            if ($layanan->id == 1) {
                $rules['data_surat.jenis_kelamin'] = [
                    'required',
                    'in:Laki-laki,Perempuan',
                ];

                $rules['data_surat.agama'] = [
                    'required',
                    'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
                ];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Validasi dokumen persyaratan
        |--------------------------------------------------------------------------
        */

        foreach ($layanan->persyaratans as $persyaratan) {

            $fieldName = 'persyaratan.' . $persyaratan->id;

            $extensions = collect(
                explode(',', $persyaratan->tipe_file)
            )
                ->map(fn ($item) => trim($item))
                ->filter()
                ->values()
                ->implode(',');

            $rules[$fieldName] = [
                $persyaratan->wajib ? 'required' : 'nullable',
                'file',
                'mimes:' . $extensions,
                'max:' . $persyaratan->maks_size,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Jalankan validasi
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate($rules);

        /*
        |--------------------------------------------------------------------------
        | Simpan permohonan
        |--------------------------------------------------------------------------
        */

        $dataSurat = $validated['data_surat'] ?? [];

        $permohonan = Permohonan::create([
            'layanan_id' => $layanan->id,
            'nama_lengkap' => $validated['nama_lengkap'],
            'tanggal_lahir' => $validated['tanggal_lahir'],
            'nik' => $nikEncryptionService->encrypt(
                $validated['nik']
            ),
            'rt' => $validated['rt'],
            'rw' => $validated['rw'],
            'data_surat' => $dataSurat,
            'status' => 'selesai',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Simpan dokumen persyaratan
        |--------------------------------------------------------------------------
        */

        foreach ($layanan->persyaratans as $persyaratan) {

            $file = $request->file(
                'persyaratan.' . $persyaratan->id
            );

            if ($file) {

                $path = $file->store(
                    'persyaratan/' . $permohonan->id,
                    'local'
                );

                $permohonan->dokumenPersyaratans()->create([
                    'persyaratan_id' => $persyaratan->id,
                    'file_path' => $path,
                    'file_original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return redirect()
            ->route('permohonan.preview', $permohonan)
            ->with('success', 'Surat berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | Preview Surat
    |--------------------------------------------------------------------------
    */

    public function preview(
        Permohonan $permohonan,
        SuratGenerator $suratGenerator
    ) {
        $permohonan->load([
            'layanan',
            'dokumenPersyaratans',
        ]);

        $surat = $suratGenerator->generate($permohonan);

        return view('permohonan.preview', compact(
            'permohonan',
            'surat'
        ));
    }
}
