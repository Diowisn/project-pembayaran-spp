<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AngsuranInfaq;
use App\Models\Siswa;
use App\Models\User;
use App\Models\InfaqGedung;
use App\Models\Tabungan;
use Alert;
use PDF;
use Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InfaqController extends Controller
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
        $query = AngsuranInfaq::with(['siswa', 'infaqGedung']);

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
            $validColumns = ['created_at', 'jumlah_bayar', 'angsuran_ke', 'kembalian', 'tgl_bayar'];
            $sortBy = in_array($request->sort_by, $validColumns) ? $request->sort_by : 'created_at';
            
            $query->orderBy($sortBy, $request->order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Ambil data dengan pagination
        $angsuranList = $query->paginate(10)->appends($request->all());

        // Hitung status lunas untuk setiap angsuran
        $angsuranData = [];
        foreach ($angsuranList as $angsuran) {
            $totalDibayar = AngsuranInfaq::where('id_siswa', $angsuran->id_siswa)->sum('jumlah_bayar');
            $totalTagihan = $angsuran->infaqGedung->nominal ?? 0;
            $isLunas = ($totalDibayar >= $totalTagihan);
            
            $angsuranData[$angsuran->id] = [
                'is_lunas' => $isLunas,
                'total_dibayar' => $totalDibayar,
                'kekurangan' => max(0, $totalTagihan - $totalDibayar),
                'kembalian' => $angsuran->kembalian
            ];
        }

        return view('dashboard.infaq.index', [
            'angsuran' => $angsuranList,
            'angsuran_data' => $angsuranData,
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

        $siswa = Siswa::with(['infaqGedung', 'angsuranInfaq', 'kelas'])->where('nisn', $request->nisn)->first();

        $totalDibayar = AngsuranInfaq::where('id_siswa', $siswa->id)->sum('jumlah_bayar');
        $totalTagihan = $siswa->infaqGedung ? $siswa->infaqGedung->nominal : 0;
        $sisaPembayaran = max($totalTagihan - $totalDibayar, 0);

        $pembayaranQuery = AngsuranInfaq::with(['siswa', 'infaqGedung'])
            ->where('id_siswa', $siswa->id)
            ->orderBy('tgl_bayar', 'asc')
            ->orderBy('id', 'asc');

        $angsuran = $pembayaranQuery->paginate(10)->appends(['nisn' => $request->nisn]);

        $angsuranData = [];
        $totalDibayarSampaiSekarang = 0;
        
        foreach ($angsuran as $item) {
            $totalDibayarSampaiSekarang += $item->jumlah_bayar;
            $totalTagihanItem = $item->infaqGedung->nominal ?? 0;
            $kekurangan = max(0, $totalTagihanItem - $totalDibayarSampaiSekarang);
            $isLunas = ($kekurangan <= 0);
            
            $angsuranData[$item->id] = [
                'kekurangan' => $kekurangan,
                'is_lunas' => $isLunas,
                'total_dibayar_sampai_sekarang' => $totalDibayarSampaiSekarang
            ];
        }

        return view('dashboard.infaq.index', [
            'angsuran' => $angsuran,
            'angsuran_data' => $angsuranData,
            'siswa' => $siswa,
            'total_dibayar' => $totalDibayar,
            'total_tagihan_infaq' => $totalTagihan,
            'sisa_pembayaran' => $sisaPembayaran,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    private function updateLunasStatus($idSiswa)
    {
        $totalDibayar = AngsuranInfaq::where('id_siswa', $idSiswa)->sum('jumlah_bayar');
        $siswa = Siswa::with('infaqGedung')->find($idSiswa);
        $totalTagihan = $siswa->infaqGedung->nominal ?? 0;
        $isLunas = ($totalDibayar >= $totalTagihan);
        
        AngsuranInfaq::where('id_siswa', $idSiswa)->update(['is_lunas' => $isLunas]);
        
        return $isLunas;
    }

    public function updateExistingData()
    {
        $allAngsuran = AngsuranInfaq::with(['siswa.infaqGedung'])->get();
        
        foreach ($allAngsuran as $angsuran) {
            $totalDibayar = AngsuranInfaq::where('id_siswa', $angsuran->id_siswa)->sum('jumlah_bayar');
            $totalTagihan = $angsuran->siswa->infaqGedung->nominal ?? 0;
            $isLunas = ($totalDibayar >= $totalTagihan);
            
            $angsuran->update([
                'is_lunas' => $isLunas,
                'kembalian' => $angsuran->kembalian ?? 0
            ]);
        }
        
        return "Data updated successfully";
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswa,id',
            'nisn' => 'required|exists:siswa,nisn',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tgl_bayar' => 'required|date',
            'jumlah_tagihan' => 'required|numeric'
        ], [
            'required' => 'Field :attribute wajib diisi',
            'min' => 'Pembayaran tidak boleh kurang dari :min'
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->route('infaq.cari-siswa', ['nisn' => $request->nisn])
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();

            $siswa = Siswa::with(['infaqGedung', 'angsuranInfaq'])->findOrFail($request->id_siswa);
            
            $totalDibayarSebelumnya = AngsuranInfaq::where('id_siswa', $request->id_siswa)->sum('jumlah_bayar');
            $totalDibayarSekarang = $totalDibayarSebelumnya + $request->jumlah_bayar;
            $totalTagihan = $siswa->infaqGedung->nominal ?? 0;
            
            $sisaTagihan = max(0, $totalTagihan - $totalDibayarSebelumnya);
            $kembalian = max(0, $request->jumlah_bayar - $sisaTagihan);
            $isLunas = ($totalDibayarSekarang >= $totalTagihan);

            $angsuran = AngsuranInfaq::create([
                'id_siswa' => $request->id_siswa,
                'angsuran_ke' => AngsuranInfaq::where('id_siswa', $request->id_siswa)->count() + 1,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kembalian' => $kembalian,
                'tgl_bayar' => $request->tgl_bayar,
                'is_lunas' => $isLunas,
                'id_petugas' => auth()->id(),
            ]);

            $this->updateLunasStatus($request->id_siswa);

            if ($kembalian > 0) {
                $saldo_terakhir = Tabungan::where('id_siswa', $request->id_siswa)
                    ->latest()
                    ->first();
                
                $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
                $saldo_sekarang = $saldo_sebelumnya + $kembalian;

                Tabungan::create([
                    'id_siswa' => $request->id_siswa,
                    'id_pembayaran_infaq' => $angsuran->id,
                    'id_petugas' => auth()->id(),
                    'debit' => $kembalian,
                    'kredit' => 0,
                    'saldo' => $saldo_sekarang,
                    'keterangan' => 'Kembalian pembayaran infaq angsuran ke-' . $angsuran->angsuran_ke,
                ]);

                session()->flash('kembalian_info', [
                    'jumlah' => $kembalian,
                    'message' => 'Pembayaran berhasil! Kembalian Rp ' . number_format($kembalian, 0, ',', '.') . ' telah ditambahkan ke tabungan.'
                ]);
            }

            DB::commit();

            Alert::success('Berhasil!', 'Pembayaran infaq berhasil disimpan!' . ($kembalian > 0 ? ' Kembalian telah ditambahkan ke tabungan.' : ''));
            return redirect()->route('infaq.cari-siswa', ['nisn' => $siswa->nisn]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pembayaran infaq: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat menyimpan pembayaran infaq');
            return redirect()->route('infaq.cari-siswa', ['nisn' => $request->nisn])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $angsuran = AngsuranInfaq::with(['siswa', 'infaqGedung'])->findOrFail($id);
        
        return view('dashboard.infaq.edit', [
            'edit' => $angsuran,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $angsuran = AngsuranInfaq::with(['siswa', 'infaqGedung'])->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'angsuran_ke' => 'required|numeric|min:1',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tgl_bayar' => 'required|date'
        ], [
            'required' => 'Field :attribute wajib diisi',
            'min' => ':attribute tidak boleh kurang dari :min'
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();

            $totalDibayarLainnya = AngsuranInfaq::where('id_siswa', $angsuran->id_siswa)
                ->where('id', '!=', $id)
                ->sum('jumlah_bayar');
            
            $totalDibayarSekarang = $totalDibayarLainnya + $request->jumlah_bayar;
            $totalTagihan = $angsuran->infaqGedung->nominal ?? 0;
            
            $sisaTagihan = max(0, $totalTagihan - $totalDibayarLainnya);
            $kembalian = max(0, $request->jumlah_bayar - $sisaTagihan);
            $isLunas = ($totalDibayarSekarang >= $totalTagihan);

            $jumlahBayarLama = $angsuran->jumlah_bayar;
            $kembalianLama = $angsuran->kembalian;

            $angsuran->update([
                'angsuran_ke' => $request->angsuran_ke,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kembalian' => $kembalian,
                'tgl_bayar' => $request->tgl_bayar,
                'is_lunas' => $isLunas,
                'id_petugas' => auth()->id(),
            ]);

            $this->updateLunasStatus($angsuran->id_siswa);

            $tabungan = Tabungan::where('id_pembayaran_infaq', $angsuran->id)->first();
            
            if ($kembalian > 0) {
                if ($tabungan) {
                    $selisihKembalian = $kembalian - $kembalianLama;
                    
                    $tabungan->update([
                        'debit' => $kembalian,
                        'saldo' => $tabungan->saldo + $selisihKembalian,
                        'keterangan' => 'Kembalian pembayaran infaq angsuran ke-' . $request->angsuran_ke,
                    ]);
                } else {
                    $saldo_terakhir = Tabungan::where('id_siswa', $angsuran->id_siswa)
                        ->latest()
                        ->first();
                    
                    $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
                    $saldo_sekarang = $saldo_sebelumnya + $kembalian;

                    Tabungan::create([
                        'id_siswa' => $angsuran->id_siswa,
                        'id_pembayaran_infaq' => $angsuran->id,
                        'id_petugas' => auth()->id(),
                        'debit' => $kembalian,
                        'kredit' => 0,
                        'saldo' => $saldo_sekarang,
                        'keterangan' => 'Kembalian pembayaran infaq angsuran ke-' . $request->angsuran_ke,
                    ]);
                }
            } elseif ($tabungan) {
                $tabungan->delete();
            }

            DB::commit();

            Alert::success('Berhasil!', 'Pembayaran infaq berhasil diperbarui');
            return redirect()->route('infaq.index');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating pembayaran infaq: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat memperbarui pembayaran infaq: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction(); 
            
            $angsuran = AngsuranInfaq::findOrFail($id);
            $idSiswa = $angsuran->id_siswa;
            
            $tabungan = Tabungan::where('id_pembayaran_infaq', $angsuran->id)->first();
            if ($tabungan) {
                $tabungan->delete();
            }
            
            if($angsuran->delete()) {
                $this->updateLunasStatus($idSiswa);
                
                Alert::success('Berhasil!', 'Pembayaran infaq berhasil dihapus!');
            } else {
                Alert::error('Terjadi Kesalahan!', 'Pembayaran infaq gagal dihapus!');
            }
            
            DB::commit(); 
            
        } catch (\Exception $e) {
            DB::rollBack(); 
            Alert::error('Terjadi Kesalahan!', 'Pembayaran infaq gagal dihapus!');
        }
        
        return back();
    }

    public function generate($id)
    {
        ini_set('max_execution_time', 300);

        $user = Auth::user();
        $tanggal = Carbon::now()->format('d-m-Y');
        $angsuran = AngsuranInfaq::with(['siswa', 'infaqGedung'])->findOrFail($id);

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
        
        $pdf = PDF::loadView('pdf.bukti-infaq', compact('angsuran', 'logoData', 'websiteData', 'instagramData', 'facebookData', 'youtubeData', 'whatsappData', 'barcodeData', 'user'))
                  ->setPaper('a5', 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                      'dpi' => 150
                  ]);
        
        $namaFile = 'Bukti-Pembayaran-Infaq-' . $angsuran->siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    public function generateRiwayatInfaq($siswaId)
    {
        try {
            $siswa = Siswa::with(['kelas', 'infaqGedung'])->findOrFail($siswaId);
            $riwayatInfaq = AngsuranInfaq::where('id_siswa', $siswaId)
                ->with(['petugas', 'infaqGedung'])
                ->orderBy('tgl_bayar', 'desc')
                ->orderBy('angsuran_ke', 'desc')
                ->get();

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

            $totalDibayar = $riwayatInfaq->sum('jumlah_bayar');
            $totalTagihan = $siswa->infaqGedung->nominal ?? 0;
            $totalKembalian = $riwayatInfaq->sum('kembalian');
            $sisaPembayaran = max(0, $totalTagihan - $totalDibayar);

            $pdf = PDF::loadView('pdf.rekap-pembayaran-infaq', compact(
                'siswa', 
                'riwayatInfaq', 
                'logoData', 
                'tanggal', 
                'websiteData', 
                'instagramData', 
                'facebookData', 
                'youtubeData', 
                'whatsappData', 
                'barcodeData',
                'totalDibayar',
                'totalTagihan',
                'totalKembalian',
                'sisaPembayaran'
            ))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'dpi' => 150,
                ]);

            $namaFile = 'Rekap-Pembayaran-Infaq-' . $siswa->nama . '-' . $tanggal . '.pdf';
            return $pdf->download($namaFile);
            
        } catch (\Exception $e) {
            Log::error('Error generating riwayat infaq PDF: ' . $e->getMessage());
            Alert::error('Error', 'Gagal menghasilkan PDF riwayat infaq: ' . $e->getMessage());
            return back();
        }
    }
}