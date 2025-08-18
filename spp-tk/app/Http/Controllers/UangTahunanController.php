<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\UangTahunan;
use App\Models\Siswa;
use App\Models\Pembayaran;
use Alert;
use PDF;
use Carbon\Carbon;

class UangTahunanController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth',
            'privilege:admin&petugas'
        ]);
    }

    /**
     * Display a listing of all annual fund transactions
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        // Data untuk tabel
        $uangTahunan = UangTahunan::with(['siswa', 'petugas', 'pembayaran'])
            ->when($search, function($query) use ($search) {
                return $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->when($tahun, function($query) use ($tahun) {
                return $query->where('tahun_ajaran', $tahun);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Data untuk filter tahun
        $tahunAjaran = UangTahunan::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        // Data siswa jika ada pencarian NISN
        $siswa = null;
        if ($request->has('nisn')) {
            $siswa = Siswa::where('nisn', $request->nisn)->first();
        }

        return view('dashboard.uang-tahunan.index', [
            'uangTahunan' => $uangTahunan,
            'search' => $search,
            'tahun' => $tahun,
            'tahunAjaran' => $tahunAjaran,
            'siswa' => $siswa,
            'user' => auth()->user()
        ]);
    }

    /**
     * Show form for manual annual fund input
     */
    public function create()
    {
        // return view('dashboard.uang-tahunan.create', [
        //     'tahunAjaran' => Carbon::now()->year,
        //     'user' => auth()->user()
        // ]);
    }

    public function cariSiswa(Request $request)
    {
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn'
        ]);

        $siswa = Siswa::where('nisn', $request->nisn)->first();
        $tahunAjaran = Carbon::now()->year;

        // Get data untuk tabel
        $uangTahunan = UangTahunan::with(['siswa', 'petugas', 'pembayaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get distinct years for filter
        $tahunList = UangTahunan::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        return view('dashboard.uang-tahunan.index', [
            'siswa' => $siswa,
            'tahunAjaran' => $tahunAjaran,
            'uangTahunan' => $uangTahunan,
            'tahunAjaran' => $tahunList,
            'user' => auth()->user(),
            'search' => $request->nisn
        ]);
    }
    /**
     * Process manual annual fund input
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'tahun_ajaran' => 'required|numeric|min:2000|max:2100',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($request->id_siswa);
            
            // Get last balance for this student and year
            $saldo_terakhir = UangTahunan::where('id_siswa', $request->id_siswa)
                ->where('tahun_ajaran', $request->tahun_ajaran)
                ->latest()
                ->first();
            
            $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
            $saldo_sekarang = $saldo_sebelumnya + $request->jumlah;

            UangTahunan::create([
                'id_siswa' => $request->id_siswa,
                'tahun_ajaran' => $request->tahun_ajaran,
                'id_petugas' => auth()->id(),
                'debit' => $request->jumlah,
                'kredit' => 0,
                'saldo' => $saldo_sekarang,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            Alert::success('Berhasil!', 'Setoran uang kegiatan tahunan berhasil disimpan');
            // return redirect()->route('uang-tahunan.show', ['id' => $request->id_siswa, 'tahun' => $request->tahun_ajaran]);
            return redirect()->route('uang-tahunan.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving manual annual fund: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat menyimpan setoran');
            return back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit($id)
    {
        $uangTahunan = UangTahunan::with('siswa')->findOrFail($id);
        // $siswaList = Siswa::orderBy('nama')->get();
        
        return view('dashboard.uang-tahunan.edit', [
            'uangTahunan' => $uangTahunan,
            // 'siswaList' => $siswaList,
            'user' => auth()->user()
        ]);
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
            'tipe' => 'required|in:debit,kredit'
        ]);

        try {
            DB::beginTransaction();

            $uangTahunan = UangTahunan::findOrFail($id);
            $siswa = Siswa::findOrFail($request->id_siswa);
            
            // Simpan data lama untuk perhitungan ulang
            $oldAmount = $uangTahunan->debit > 0 ? $uangTahunan->debit : $uangTahunan->kredit;
            $oldType = $uangTahunan->debit > 0 ? 'debit' : 'kredit';
            $oldSiswaId = $uangTahunan->id_siswa;
            $tahunAjaran = $uangTahunan->tahun_ajaran;
            
            // Update data transaksi
            if ($request->tipe === 'debit') {
                $uangTahunan->debit = $request->jumlah;
                $uangTahunan->kredit = 0;
            } else {
                $uangTahunan->kredit = $request->jumlah;
                $uangTahunan->debit = 0;
            }
            
            $uangTahunan->id_siswa = $request->id_siswa;
            $uangTahunan->keterangan = $request->keterangan;
            $uangTahunan->save();
            
            // Hitung ulang saldo untuk siswa yang lama dan yang baru
            $this->recalculateSaldo($oldSiswaId, $tahunAjaran);
            if ($oldSiswaId != $request->id_siswa) {
                $this->recalculateSaldo($request->id_siswa, $tahunAjaran);
            }

            DB::commit();

            Alert::success('Berhasil!', 'Transaksi uang kegiatan tahunan berhasil diperbarui');
            return redirect()->route('uang-tahunan.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating annual fund transaction: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat memperbarui transaksi');
            return back()->withInput();
        }
    }

    /**
     * Helper method to recalculate saldo for a student in specific year
     */
    private function recalculateSaldo($siswaId, $tahunAjaran)
    {
        $transactions = UangTahunan::where('id_siswa', $siswaId)
            ->where('tahun_ajaran', $tahunAjaran)
            ->orderBy('created_at')
            ->get();
        
        $saldo = 0;
        
        foreach ($transactions as $transaction) {
            $saldo += $transaction->debit;
            $saldo -= $transaction->kredit;
            
            $transaction->saldo = $saldo;
            $transaction->save();
        }
    }

    /**
     * Show detail annual fund for a specific student
     */
    public function show(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        $uangTahunan = UangTahunan::where('id_siswa', $id)
            ->where('tahun_ajaran', $tahun)
            ->with(['petugas', 'pembayaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $saldo = UangTahunan::where('id_siswa', $id)
            ->where('tahun_ajaran', $tahun)
            ->latest()
            ->first()
            ->saldo ?? 0;

        // Get distinct years for this student
        $tahunAjaran = UangTahunan::where('id_siswa', $id)
            ->select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran');

        return view('dashboard.uang-tahunan.show', [
            'siswa' => $siswa,
            'uangTahunan' => $uangTahunan,
            'saldo' => $saldo,
            'tahun' => $tahun,
            'tahunAjaran' => $tahunAjaran,
            'user' => auth()->user()
        ]);
    }

    /**
     * Process withdrawal from annual fund
     */
    public function tarik(Request $request, $id)
    {
        $request->validate([
            'tahun_ajaran' => 'required|numeric|min:2000|max:2100',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($id);
            
            $saldo_terakhir = UangTahunan::where('id_siswa', $id)
                ->where('tahun_ajaran', $request->tahun_ajaran)
                ->latest()
                ->first();
            
            $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
            
            if ($request->jumlah > $saldo_sebelumnya) {
                return back()->with('error', 'Saldo tidak mencukupi');
            }

            $saldo_sekarang = $saldo_sebelumnya - $request->jumlah;

            UangTahunan::create([
                'id_siswa' => $id,
                'tahun_ajaran' => $request->tahun_ajaran,
                'id_petugas' => auth()->id(),
                'debit' => 0,
                'kredit' => $request->jumlah,
                'saldo' => $saldo_sekarang,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            Alert::success('Berhasil!', 'Penarikan uang kegiatan tahunan berhasil');
            return redirect()->route('uang-tahunan.show', ['id' => $id, 'tahun' => $request->tahun_ajaran]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error withdrawing annual fund: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat melakukan penarikan');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Temukan transaksi yang akan dihapus
            $uangTahunan = UangTahunan::findOrFail($id);
            $siswaId = $uangTahunan->id_siswa;
            $tahunAjaran = $uangTahunan->tahun_ajaran;
            
            // Simpan data untuk perhitungan ulang
            $isDebit = $uangTahunan->debit > 0;
            $amount = $isDebit ? $uangTahunan->debit : $uangTahunan->kredit;
            
            // Hapus transaksi
            $uangTahunan->delete();
            
            // Hitung ulang saldo untuk siswa ini dan tahun ini
            $this->recalculateSaldo($siswaId, $tahunAjaran);

            DB::commit();

            Alert::success('Berhasil!', 'Transaksi uang kegiatan tahunan berhasil dihapus');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting annual fund transaction: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat menghapus transaksi');
            return back();
        }
    }

    /**
     * Generate annual fund report PDF
     */
    public function generateReport($id, $tahun)
    {
        $siswa = Siswa::findOrFail($id);
        $tanggal = Carbon::now()->format('d-m-Y');
        
        $uangTahunan = UangTahunan::where('id_siswa', $id)
            ->where('tahun_ajaran', $tahun)
            ->orderBy('created_at', 'desc')
            ->get(); 

        if ($uangTahunan->isEmpty()) {
            abort(404, 'Tidak ada transaksi uang kegiatan tahunan untuk siswa ini');
        }

        $saldo = $uangTahunan->first()->saldo;

        $logoPath = public_path('img/amanah31.png');
        $websitePath = public_path('img/icons/website.png');
        $instagramPath = public_path('img/icons/instagram.png');
        $facebookPath = public_path('img/icons/facebook.png');
        $youtubePath = public_path('img/icons/youtube.png');
        $whatsappPath = public_path('img/icons/whatsapp.png');
        $barcodePath = public_path('img/barcode/barcode-ita.png');

        $logoData = base64_encode(file_get_contents($logoPath));
        $websiteData = base64_encode(file_get_contents($websitePath));
        $instagramData = base64_encode(file_get_contents($instagramPath));
        $facebookData = base64_encode(file_get_contents($facebookPath));
        $youtubeData = base64_encode(file_get_contents($youtubePath));
        $whatsappData = base64_encode(file_get_contents($whatsappPath));
        $barcodeData = base64_encode(file_get_contents($barcodePath));

        $pdf = PDF::loadView('pdf.uang-tahunan', [
            'siswa' => $siswa,
            'uangTahunan' => $uangTahunan,
            'saldo' => $saldo,
            'tahun' => $tahun,
            'logoData' => $logoData,
            'websiteData' => $websiteData,
            'instagramData' => $instagramData,
            'facebookData' => $facebookData,
            'youtubeData' => $youtubeData,
            'whatsappData' => $whatsappData,
            'barcodeData' => $barcodeData,
            'tanggal' => now()->format('d F Y')
        ])->setPaper('a4', 'portrait');

        $namaFile = 'Laporan-Uang-Tahunan-' . $siswa->nama . '-' . $tahun . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }
}