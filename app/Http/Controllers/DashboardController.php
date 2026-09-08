<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use App\Models\Permohonan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Permohonan::with('layanan')->latest();

        if ($request->filled('q')) {
            $q = trim($request->input('q'));
            $query->where(function ($builder) use ($q) {
                $builder->where('nama_lengkap', 'like', "%{$q}%")
                    ->orWhere('id', is_numeric($q) ? (int) $q : -1);
            });
        }

        if ($request->filled('layanan_id')) {
            $query->where('layanan_id', $request->integer('layanan_id'));
        }

        if ($request->filled('status')) {
            $query->whereIn('status', ['diajukan', 'diproses', 'selesai'])
                ->where('status', $request->input('status'));
        }

        $permohonans = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Permohonan::count(),
            'diajukan' => Permohonan::where('status', 'diajukan')->count(),
            'diproses' => Permohonan::where('status', 'diproses')->count(),
            'selesai' => Permohonan::where('status', 'selesai')->count(),
        ];

        $layanans = Layanan::where('aktif', true)->orderBy('id')->get(['id', 'nama']);

        return view('dashboard.index', compact('permohonans', 'stats', 'layanans'));
    }

    public function show(Permohonan $permohonan)
    {
        $permohonan->load(['layanan.persyaratans', 'dokumenPersyaratans.persyaratan']);
        return view('dashboard.show', compact('permohonan'));
    }

    public function updateStatus(Request $request, Permohonan $permohonan)
    {
        $data = $request->validate([
            'status' => ['required', 'in:diajukan,diproses,selesai'],
        ]);

        $permohonan->update(['status' => $data['status']]);

        return back()->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
