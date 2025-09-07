<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tabungan;
use App\Models\Siswa;
use App\Models\Pembayaran;
use Alert;
use PDF;
use Carbon\Carbon;

class TabunganController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth',
            'privilege:admin&petugas'
        ]);
    }

    /**
     * Display a listing of all savings transactions
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $nisn_search = $request->input('nisn_search');
        
        // Query untuk data tabungan semua siswa dengan saldo terakhir
        $query = Siswa::with(['kelas'])
            ->select('siswa.*')
            ->leftJoinSub(
                Tabungan::select('id_siswa', DB::raw('MAX(created_at) as last_transaction'), DB::raw('MAX(saldo) as last_balance'))
                    ->groupBy('id_siswa'),
                'last_tabungan',
                function ($join) {
                    $join->on('siswa.id', '=', 'last_tabungan.id_siswa');
                }
            );

        // Filter berdasarkan pencarian umum
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('siswa.nisn', 'like', "%{$search}%")
                  ->orWhere('siswa.nama', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan NISN spesifik (setelah pencarian dengan form NISN)
        if ($nisn_search) {
            $query->where('siswa.nisn', $nisn_search);
        }

        $tabunganAll = $query->orderBy('siswa.nama')->paginate(10);

        // Tambahkan data saldo dan transaksi terakhir
        $tabunganAll->getCollection()->transform(function ($siswa) {
            $saldoTerakhir = Tabungan::where('id_siswa', $siswa->id)
                ->latest()
                ->first();
                
            $siswa->saldo_terakhir = $saldoTerakhir ? $saldoTerakhir->saldo : 0;
            $siswa->transaksi_terakhir = $saldoTerakhir ? $saldoTerakhir->created_at : null;
            
            return $siswa;
        });

        return view('dashboard.tabungan.index', [
            'tabungan' => $tabunganAll,
            'search' => $search,
            'nisn_search' => $nisn_search,
            'user' => auth()->user()
        ]);
    }


    /**
     * Show form for manual savings input
     */
    public function create()
    {
        $siswaList = Siswa::orderBy('nama')->get();
        return view('dashboard.tabungan.create', [
            'siswaList' => $siswaList,
            'user' => auth()->user()
        ]);
    }

    /**
     * Cari siswa berdasarkan NISN untuk tabungan
     */
    public function cariSiswa(Request $request)
    {
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn'
        ]);

        $siswa = Siswa::with(['kelas'])->where('nisn', $request->nisn)->first();
        
        if (!$siswa) {
            return redirect()->route('tabungan.index')
                ->with('error', 'Siswa dengan NISN tersebut tidak ditemukan');
        }
        
        // Hitung saldo terakhir
        $saldoTerakhir = Tabungan::where('id_siswa', $siswa->id)
            ->latest()
            ->first();
        
        $saldo = $saldoTerakhir ? $saldoTerakhir->saldo : 0;
        
        // Ambil riwayat transaksi
        $riwayatTabungan = Tabungan::where('id_siswa', $siswa->id)
            ->with('petugas')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Query untuk data tabungan - HANYA siswa yang dicari
        $query = Siswa::with(['kelas'])
            ->select('siswa.*')
            ->leftJoinSub(
                Tabungan::select('id_siswa', DB::raw('MAX(created_at) as last_transaction'), DB::raw('MAX(saldo) as last_balance'))
                    ->groupBy('id_siswa'),
                'last_tabungan',
                function ($join) {
                    $join->on('siswa.id', '=', 'last_tabungan.id_siswa');
                }
            )
            ->where('siswa.nisn', $request->nisn);

        $tabunganSiswa = $query->orderBy('siswa.nama')->paginate(10);

        $tabunganSiswa->getCollection()->transform(function ($siswaItem) {
            $saldoTerakhirItem = Tabungan::where('id_siswa', $siswaItem->id)
                ->latest()
                ->first();
                
            $siswaItem->saldo_terakhir = $saldoTerakhirItem ? $saldoTerakhirItem->saldo : 0;
            $siswaItem->transaksi_terakhir = $saldoTerakhirItem ? $saldoTerakhirItem->created_at : null;
            
            return $siswaItem;
        });

        return view('dashboard.tabungan.index', [
            'tabungan' => $tabunganSiswa,
            'siswa' => $siswa,
            'saldo' => $saldo,
            'riwayat_tabungan' => $riwayatTabungan,
            'nisn_search' => $request->nisn,
            'user' => auth()->user()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_siswa' => 'required|exists:siswa,id',
            'tipe' => 'required|in:debit,kredit',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255'
        ]);

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($request->id_siswa);
            
            // Dapatkan saldo terakhir
            $saldo_terakhir = Tabungan::where('id_siswa', $request->id_siswa)
                ->latest()
                ->first();
            
            $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
            
            if ($request->tipe === 'debit') {
                $debit = $request->jumlah;
                $kredit = 0;
                $saldo_sekarang = $saldo_sebelumnya + $request->jumlah;
            } else {
                // Validasi saldo untuk penarikan
                if ($request->jumlah > $saldo_sebelumnya) {
                    return back()->withErrors(['jumlah' => 'Saldo tidak mencukupi untuk penarikan ini.'])->withInput();
                }
                
                $debit = 0;
                $kredit = $request->jumlah;
                $saldo_sekarang = $saldo_sebelumnya - $request->jumlah;
            }

            Tabungan::create([
                'id_siswa' => $request->id_siswa,
                'id_petugas' => auth()->id(),
                'debit' => $debit,
                'kredit' => $kredit,
                'saldo' => $saldo_sekarang,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            Alert::success('Berhasil!', 'Transaksi tabungan berhasil disimpan');
            return redirect()->route('tabungan.cari-siswa', ['nisn' => $siswa->nisn]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving savings transaction: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat menyimpan transaksi');
            return back()->withInput();
        }
    }

    /**
     * Show the form for editing the specified savings transaction.
     */
    public function edit($id)
    {
        $tabungan = Tabungan::with(['siswa', 'petugas'])->findOrFail($id);
        
        return view('dashboard.tabungan.edit', [
            'tabungan' => $tabungan,
            'user' => auth()->user()
        ]);
    }

    /**
     * Update the specified savings transaction in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
            'tipe' => 'required|in:debit,kredit'
        ]);

        try {
            DB::beginTransaction();

            $tabungan = Tabungan::with('siswa')->findOrFail($id);
            
            // Update data transaksi
            if ($request->tipe === 'debit') {
                $tabungan->update([
                    'debit' => $request->jumlah,
                    'kredit' => 0,
                    'keterangan' => $request->keterangan,
                ]);
            } else {
                $tabungan->update([
                    'debit' => 0,
                    'kredit' => $request->jumlah,
                    'keterangan' => $request->keterangan,
                ]);
            }

            // Hitung ulang saldo untuk semua transaksi setelah yang diubah
            $this->recalculateSaldo($tabungan->id_siswa);

            DB::commit();

            Alert::success('Berhasil!', 'Transaksi tabungan berhasil diperbarui');
            return redirect()->route('tabungan.cari-siswa', ['nisn' => $tabungan->siswa->nisn]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating savings transaction: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat memperbarui transaksi');
            return back()->withInput();
        }
    }

    /**
     * Show detail savings for a specific student
     */
    public function show($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        $tabungan = Tabungan::where('id_siswa', $id)
            ->with(['petugas', 'pembayaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $saldo = Tabungan::where('id_siswa', $id)
            ->latest()
            ->first();
        
        $saldo_terakhir = $saldo ? $saldo->saldo : 0;

        return view('dashboard.tabungan.show', [
            'siswa' => $siswa,
            'tabungan' => $tabungan,
            'saldo' => $saldo_terakhir,
            'user' => auth()->user()
        ]);
    }

    /**
     * Process withdrawal from savings
     */
    public function tarik(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($id);
            
            $saldo_terakhir = Tabungan::where('id_siswa', $id)
                ->latest()
                ->first();
            
            $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
            
            if ($request->jumlah > $saldo_sebelumnya) {
                return back()->with('error', 'Saldo tidak mencukupi');
            }

            $saldo_sekarang = $saldo_sebelumnya - $request->jumlah;

            Tabungan::create([
                'id_siswa' => $id,
                'id_petugas' => auth()->id(),
                'debit' => 0,
                'kredit' => $request->jumlah,
                'saldo' => $saldo_sekarang,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            Alert::success('Berhasil!', 'Penarikan tabungan berhasil');
            return redirect()->route('tabungan.show', $id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error withdrawing savings: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat melakukan penarikan');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified savings transaction from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $tabungan = Tabungan::with('siswa')->findOrFail($id);
            $siswaId = $tabungan->id_siswa;
            $siswaNisn = $tabungan->siswa->nisn;
            
            $tabungan->delete();
            
            $this->recalculateSaldo($siswaId);

            DB::commit();

            Alert::success('Berhasil!', 'Transaksi tabungan berhasil dihapus');
            return redirect()->route('tabungan.cari-siswa', ['nisn' => $siswaNisn]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting savings transaction: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat menghapus transaksi');
            return back();
        }
    }

    /**
     * Helper method to recalculate saldo for a student
     */
    private function recalculateSaldo($siswaId)
    {
        $transactions = Tabungan::where('id_siswa', $siswaId)
            ->orderBy('created_at')
            ->get();
        
        $saldo = 0;
        
        foreach ($transactions as $transaction) {
            $saldo += $transaction->debit;
            $saldo -= $transaction->kredit;
            
            $transaction->saldo = $saldo;
            $transaction->save();
        }
        
        return $saldo;
    }

    /**
     * Helper method to get saldo before a specific transaction
     */
    private function getSaldoSebelumTransaksi($transaksi)
    {
        $previousTransaction = Tabungan::where('id_siswa', $transaksi->id_siswa)
            ->where('created_at', '<', $transaksi->created_at)
            ->orderBy('created_at', 'desc')
            ->first();
            
        return $previousTransaction ? $previousTransaction->saldo : 0;
    }

    /**
     * Generate savings transaction receipt PDF
     */
    public function generateTransactionReport($id)
    {
        $transaksi = Tabungan::with(['siswa', 'petugas'])->findOrFail($id);
        $siswa = $transaksi->siswa;
        
        $tanggal = Carbon::now()->format('d-m-Y');

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

        $pdf = PDF::loadView('pdf.bukti-tabungan', [
            'transaksi' => $transaksi,
            'siswa' => $siswa,
            'logoData' => $logoData,
            'websiteData' => $websiteData,
            'instagramData' => $instagramData,
            'facebookData' => $facebookData,
            'youtubeData' => $youtubeData,
            'whatsappData' => $whatsappData,
            'barcodeData' => $barcodeData,
            'tanggal' => now()->format('d F Y')
        ])->setPaper('a4', 'portrait');

        $namaFile = 'Bukti-Transaksi-Tabungan-' . $siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    /**
     * Generate savings report PDF for all transactions
     */
    public function generateRekapTabungan($id)
    {
        $siswa = Siswa::with(['kelas'])->findOrFail($id);
        
        $tabungan = Tabungan::where('id_siswa', $id)
            ->with('petugas')
            ->orderBy('created_at', 'desc')
            ->get();

        $tanggal = Carbon::now()->format('d-m-Y');
        
        // Load logo dan icon
        $logoPath = public_path('img/amanah31.png');
        $websitePath = public_path('img/icons/website.png');
        $instagramPath = public_path('img/icons/instagram.png');
        $facebookPath = public_path('img/icons/facebook.png');
        $youtubePath = public_path('img/icons/youtube.png');
        $whatsappPath = public_path('img/icons/whatsapp.png');
        $barcodePath = public_path('img/barcode/barcode-ita.png');

        // Convert images to base64
        $logoData = base64_encode(file_get_contents($logoPath));
        $websiteData = base64_encode(file_get_contents($websitePath));
        $instagramData = base64_encode(file_get_contents($instagramPath));
        $facebookData = base64_encode(file_get_contents($facebookPath));
        $youtubeData = base64_encode(file_get_contents($youtubePath));
        $whatsappData = base64_encode(file_get_contents($whatsappPath));
        $barcodeData = base64_encode(file_get_contents($barcodePath));

        $pdf = PDF::loadView('pdf.rekap-tabungan', [
            'siswa' => $siswa,
            'tabungan' => $tabungan,
            'logoData' => $logoData,
            'websiteData' => $websiteData,
            'instagramData' => $instagramData,
            'facebookData' => $facebookData,
            'youtubeData' => $youtubeData,
            'whatsappData' => $whatsappData,
            'barcodeData' => $barcodeData,
            'tanggal' => now()->format('d F Y')
        ])->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        $namaFile = 'Rekap-Tabungan-' . $siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }
}