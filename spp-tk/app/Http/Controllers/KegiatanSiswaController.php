<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Kegiatan;
use App\Models\KegiatanSiswa;
use App\Models\Siswa;
use App\Models\User;
use Alert;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KegiatanSiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth',
            'privilege:admin&petugas'
        ]);
    }

    public function index(Request $request)
    {
        $query = KegiatanSiswa::with(['siswa', 'kegiatan']);

        // Fitur Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        // Fitur Sorting
        if ($request->filled('sort_by') && $request->filled('order')) {
            $validColumns = ['created_at', 'updated_at'];
            $sortBy = in_array($request->sort_by, $validColumns) ? $request->sort_by : 'created_at';
            
            $query->orderBy($sortBy, $request->order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return view('dashboard.entri-kegiatan.index', [
            'kegiatanSiswa' => $query->paginate(10)->appends($request->all()),
            'user' => User::find(auth()->user()->id),
            'search' => $request->search ?? '',
            'sort_by' => $request->sort_by ?? 'created_at',
            'order' => $request->order ?? 'desc',
        ]);
    }

public function cariSiswa(Request $request)
{
    $request->validate([
        'nisn' => 'required|exists:siswa,nisn'
    ]);

    // Gunakan with untuk memuat relasi yang benar
    $siswa = Siswa::with(['kegiatanSiswa.kegiatan'])->where('nisn', $request->nisn)->first();

    if (!$siswa) {
        Alert::error('Error!', 'Siswa tidak ditemukan');
        return redirect()->route('entri-kegiatan.index');
    }

    // Dapatkan semua kegiatan yang tersedia
    $semuaKegiatan = Kegiatan::all();
    
    // Hitung total biaya kegiatan yang diikuti
    $totalBiayaKegiatan = 0;
    $kegiatanDenganStatus = [];

    // Untuk setiap kegiatan yang tersedia, cek status partisipasi siswa
    foreach ($semuaKegiatan as $kegiatan) {
        // Cari apakah siswa sudah memilih kegiatan ini
        $kegiatanSiswa = $siswa->kegiatanSiswa->firstWhere('kegiatan_id', $kegiatan->id);
        
        $ikut = $kegiatanSiswa ? $kegiatanSiswa->ikut : false;
        $status_bayar = $kegiatanSiswa ? ($kegiatanSiswa->status_bayar ?? 'belum') : 'belum';
        
        $totalBiayaKegiatan += $ikut ? $kegiatan->biaya : 0;
        
        $kegiatanDenganStatus[] = [
            'id' => $kegiatan->id,
            'nama' => $kegiatan->nama_kegiatan,
            'biaya' => $kegiatan->biaya,
            'tahun' => $kegiatan->tahun,
            'ikut' => $ikut,
            'status_bayar' => $status_bayar
        ];
    }

    return view('dashboard.entri-kegiatan.index', [
        'kegiatanSiswa' => KegiatanSiswa::with(['siswa', 'kegiatan'])->orderBy('id', 'DESC')->paginate(10),
        'siswa' => $siswa,
        'kegiatan_dengan_status' => $kegiatanDenganStatus,
        'total_biaya' => $totalBiayaKegiatan,
        'user' => User::find(auth()->user()->id)
    ]);
}

public function updatePartisipasi(Request $request, $siswaId)
{
    $validator = Validator::make($request->all(), [
        'kegiatan' => 'required|array',
        'kegiatan.*' => 'required|in:0,1'
    ]);

    if ($validator->fails()) {
        return redirect()
                ->route('entri-kegiatan.cari-siswa', ['nisn' => $request->nisn])
                ->withErrors($validator)
                ->withInput();
    }

    try {
        $siswa = Siswa::findOrFail($siswaId);
        $pilihan = $request->input('kegiatan', []);

        // Dapatkan semua kegiatan yang tersedia
        $semuaKegiatan = Kegiatan::all();
        
        foreach ($semuaKegiatan as $kegiatan) {
            $ikut = isset($pilihan[$kegiatan->id]) ? (bool)$pilihan[$kegiatan->id] : false;
            
            KegiatanSiswa::updateOrCreate(
                ['siswa_id' => $siswaId, 'kegiatan_id' => $kegiatan->id],
                ['ikut' => $ikut]
            );
        }

        Alert::success('Berhasil!', 'Pilihan kegiatan berhasil disimpan!');
        return redirect()->route('entri-kegiatan.cari-siswa', ['nisn' => $siswa->nisn]);

    } catch (\Exception $e) {
        Log::error('Error updating partisipasi kegiatan: '.$e->getMessage());
        Alert::error('Error!', 'Terjadi kesalahan saat menyimpan pilihan kegiatan');
        return redirect()->route('entri-kegiatan.index')
               ->withInput();
    }
}

public function updateStatusBayar(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'status_bayar' => 'required|in:lunas,belum,dicicil'
    ]);

    if ($validator->fails()) {
        return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
    }

    try {
        $kegiatanSiswa = KegiatanSiswa::findOrFail($id);
        $kegiatanSiswa->update(['status_bayar' => $request->status_bayar]);

        Alert::success('Berhasil!', 'Status pembayaran berhasil diperbarui');
        return redirect()->back();

    } catch (\Exception $e) {
        Log::error('Error updating status bayar: '.$e->getMessage());
        Alert::error('Error!', 'Terjadi kesalahan saat memperbarui status pembayaran');
        return back()->withInput();
    }
}

    public function edit($id)
    {
        $kegiatanSiswa = KegiatanSiswa::with(['siswa', 'kegiatan'])->findOrFail($id);
        
        return view('dashboard.entri-kegiatan.edit', [
            'edit' => $kegiatanSiswa,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $kegiatanSiswa = KegiatanSiswa::with(['siswa', 'kegiatan'])->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'ikut' => 'required|boolean',
            'status_bayar' => 'required|in:lunas,belum,dicicil'
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            $kegiatanSiswa->update([
                'ikut' => $request->ikut,
                'status_bayar' => $request->status_bayar
            ]);

            Alert::success('Berhasil!', 'Data kegiatan siswa berhasil diperbarui');
            return redirect()->route('entri-kegiatan.index');

        } catch (\Exception $e) {
            Log::error('Error updating kegiatan siswa: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat memperbarui data kegiatan siswa');
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $kegiatanSiswa = KegiatanSiswa::findOrFail($id);
        
        if($kegiatanSiswa->delete()) {
            Alert::success('Berhasil!', 'Data kegiatan siswa berhasil dihapus!');
        } else {
            Alert::error('Terjadi Kesalahan!', 'Data kegiatan siswa gagal dihapus!');
        }
        
        return back();
    }
}