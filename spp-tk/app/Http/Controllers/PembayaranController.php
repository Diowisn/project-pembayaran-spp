<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Pembayaran;
use App\Models\Tabungan; 
use App\Models\User;
use App\Models\Siswa;
use Alert;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
   
   public function __construct(){
         $this->middleware([
            'auth',
            'privilege:admin&petugas'
         ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['petugas', 'siswa', 'siswa.spp']);

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
            // Validasi kolom sorting untuk keamanan
            $validColumns = ['created_at', 'jumlah_bayar'];
            $sortBy = in_array($request->sort_by, $validColumns) ? $request->sort_by : 'created_at';
            
            $query->orderBy($sortBy, $request->order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $pembayaran = $query->paginate(10)->appends($request->all());

        return view('dashboard.entri-pembayaran.index', [
            'pembayaran' => $pembayaran, // Hasil pagination
            'user' => User::find(auth()->user()->id),
            'search' => $request->search ?? '',
            'sort_by' => $request->sort_by ?? 'created_at',
            'order' => $request->order ?? 'desc',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function cariSiswa(Request $request)
    {
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn'
        ]);

        $siswa = Siswa::with(['spp', 'paketInklusi', 'kelas'])->where('nisn', $request->nisn)->first();
        
        if (!$siswa) {
            return redirect()->route('pembayaran.cari-siswa')
                ->with('error', 'Siswa dengan NISN tersebut tidak ditemukan');
        }
        
        // Hitung total pembayaran SPP per bulan
        $bulanSekarang = strtolower(date('F'));
        $tahunSekarang = date('Y');
        
        // Cek apakah sudah bayar SPP bulan ini
        $pembayaranBulanIni = Pembayaran::where('id_siswa', $siswa->id)
            ->where('bulan', $bulanSekarang)
            ->where('tahun', $tahunSekarang)
            ->first();
        
        $sudahBayarBulanIni = !is_null($pembayaranBulanIni);
        
        // Hitung riwayat pembayaran
        $riwayatPembayaran = Pembayaran::where('id_siswa', $siswa->id)
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(bulan, 'januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember') DESC")
            ->get();
        
        // Hitung total yang sudah dibayar
        $totalDibayar = $riwayatPembayaran->sum('jumlah_bayar');
        
        // Hitung total tagihan (12 bulan x nominal SPP)
        $nominal_inklusi = 0;
        if ($siswa->inklusi && $siswa->paketInklusi) {
            $nominal_inklusi = $siswa->paketInklusi->nominal;
        }
        
        $tagihanPerBulan = $siswa->spp->nominal_spp + 
                        ($siswa->spp->nominal_konsumsi ?? 0) + 
                        ($siswa->spp->nominal_fullday ?? 0) + 
                        $nominal_inklusi;
        
        $totalTagihan = 12 * $tagihanPerBulan;
        $sisaPembayaran = max(0, $totalTagihan - $totalDibayar);

        // Query untuk data pembayaran (hanya siswa yang dicari)
        $pembayaranQuery = Pembayaran::with(['petugas', 'siswa'])
            ->where('id_siswa', $siswa->id)
            ->orderBy('id', 'DESC');

        $pembayaran = $pembayaranQuery->paginate(10);

        return view('dashboard.entri-pembayaran.index', [
            'pembayaran' => $pembayaran, // Ini sudah benar (object pagination)
            'siswa' => $siswa,
            'user' => User::find(auth()->user()->id),
            'sudah_bayar_bulan_ini' => $sudahBayarBulanIni,
            'riwayat_pembayaran' => $riwayatPembayaran,
            'total_dibayar' => $totalDibayar,
            'total_tagihan' => $totalTagihan,
            'sisa_pembayaran' => $sisaPembayaran,
            'tagihan_per_bulan' => $tagihanPerBulan,
            'pembayaran_bulan_ini' => $pembayaranBulanIni,
            'nisn_search' => $request->nisn,
            'show_payment_form' => true // Tambahkan flag untuk menampilkan form pembayaran
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswa,id',
            'nisn' => 'required|exists:siswa,nisn',
            'bulan' => 'required|string',
            'nominal_konsumsi' => 'nullable|numeric|min:0',
            'nominal_pembayaran' => 'required|numeric|min:0',
            'jumlah_tagihan' => 'required|numeric',
            'tahun' => 'required|numeric'
        ], [
            'bulan.required' => 'Bulan pembayaran harus dipilih',
            'nominal_pembayaran.min' => 'Pembayaran tidak boleh kurang dari :min',
            'required' => 'Field :attribute wajib diisi'
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->route('pembayaran.cari-siswa', ['nisn' => $request->nisn])
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();

            $siswa = Siswa::with(['spp', 'paketInklusi'])->findOrFail($request->id_siswa);
            
            $bulan = strtolower($request->bulan);
            $tahun = $request->tahun ?? date('Y');
            
            $pembayaranExist = Pembayaran::where('id_siswa', $request->id_siswa)
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->exists();

            if ($pembayaranExist) {
                return redirect()
                    ->route('pembayaran.cari-siswa', ['nisn' => $request->nisn])
                    ->with('error', 'Siswa sudah melakukan pembayaran untuk bulan ' . ucfirst($bulan) . ' ' . $tahun)
                    ->withInput();
            }

            $nominal_inklusi = 0;
            if ($siswa->inklusi && $siswa->paketInklusi) {
                $nominal_inklusi = $siswa->paketInklusi->nominal;
            }

            $nominal_konsumsi = $request->nominal_konsumsi ?? $siswa->spp->nominal_konsumsi ?? 0;
            
            $total_tagihan = $siswa->spp->nominal_spp + 
                            $nominal_konsumsi +
                            ($siswa->spp->nominal_fullday ?? 0) +
                            $nominal_inklusi;

            // Validasi nominal pembayaran
            if ($request->nominal_pembayaran < $total_tagihan) {
                return redirect()
                    ->route('pembayaran.cari-siswa', ['nisn' => $request->nisn])
                    ->with('error', 'Nominal pembayaran tidak boleh kurang dari total tagihan')
                    ->withInput();
            }

            $kembalian = $request->nominal_pembayaran - $total_tagihan;
            $is_lunas = true;

            $pembayaran = Pembayaran::create([
                'id_petugas' => auth()->id(),
                'nama_petugas' => auth()->user()->name,
                'id_siswa' => $request->id_siswa,
                'id_spp' => $siswa->id_spp,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nominal_spp' => $siswa->spp->nominal_spp,
                'nominal_konsumsi' => $nominal_konsumsi,
                'nominal_fullday' => $siswa->spp->nominal_fullday ?? 0,
                'nominal_inklusi' => $nominal_inklusi,
                'jumlah_bayar' => $request->nominal_pembayaran,
                'kembalian' => $kembalian,
                'tgl_bayar' => now(),
                'is_lunas' => $is_lunas,
            ]);

            DB::commit();

            // JIKA ADA KEMBALIAN, redirect ke halaman konfirmasi
            if ($kembalian > 0) {
                return redirect()->route('entri-pembayaran.konfirmasi-kembalian', $pembayaran->id);
            }

            Alert::success('Berhasil!', 'Pembayaran berhasil disimpan!');
            return redirect()->route('pembayaran.cari-siswa', ['nisn' => $request->nisn])
                ->with('success', 'Pembayaran berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating pembayaran: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat menyimpan pembayaran');
            return redirect()->route('pembayaran.cari-siswa', ['nisn' => $request->nisn])
                ->withInput();
        }
    }

    public function konfirmasiKembalian($id)
    {
        $pembayaran = Pembayaran::with('siswa')->findOrFail($id);
        
        return view('dashboard.entri-pembayaran.konfirmasi-kembalian', [
            'pembayaran' => $pembayaran,
            'user' => User::find(auth()->user()->id),
            'siswa' => $pembayaran->siswa,
            'kembalian' => $pembayaran->kembalian
        ]);
    }

    public function handleKembalian(Request $request)
    {
        $request->validate([
            'id_pembayaran' => 'required|exists:pembayaran,id',
            'action' => 'required|in:tabungan,tunai',
            'jumlah_kembalian' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $pembayaran = Pembayaran::findOrFail($request->id_pembayaran);
            $siswa = $pembayaran->siswa;

            if ($request->action === 'tabungan') {
                // Masukkan ke tabungan
                $saldo_terakhir = Tabungan::where('id_siswa', $siswa->id)
                    ->latest()
                    ->first();
                
                $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
                $saldo_sekarang = $saldo_sebelumnya + $request->jumlah_kembalian;

                Tabungan::create([
                    'id_siswa' => $siswa->id,
                    'id_pembayaran' => $pembayaran->id,
                    'id_petugas' => auth()->id(),
                    'debit' => $request->jumlah_kembalian,
                    'kredit' => 0,
                    'saldo' => $saldo_sekarang,
                    'keterangan' => 'Kembalian pembayaran SPP bulan ' . ucfirst($pembayaran->bulan),
                ]);

                // Update status kembalian di pembayaran
                $pembayaran->update(['kembalian_action' => 'tabungan']);

                $message = 'Kembalian berhasil dimasukkan ke tabungan siswa';
            } else {
                // Update status kembalian di pembayaran
                $pembayaran->update(['kembalian_action' => 'tunai']);

                $message = 'Kembalian berhasil dikembalikan secara tunai';
            }

            DB::commit();

            Alert::success('Berhasil!', $message);
            return redirect()->route('pembayaran.cari-siswa', ['nisn' => $siswa->nisn])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error handling kembalian: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat menangani kembalian');
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pembayaran = Pembayaran::with('siswa.spp')->findOrFail($id);
        
        $data = [
            'edit' => $pembayaran,
            'user' => User::find(auth()->user()->id)
        ];
        
        return view('dashboard.entri-pembayaran.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::with('siswa.spp')->findOrFail($id);
        
        $messages = [
            'required' => ':attribute harus diisi',
            'numeric' => ':attribute harus berupa angka',
            'min' => 'Pembayaran tidak boleh kurang dari tagihan'
        ];

        $validator = Validator::make($request->all(), [
            'bulan' => 'required',
            'nominal_konsumsi' => 'nullable|numeric|min:0',
            'jumlah_bayar' => 'required|numeric|min:'.($request->jumlah_tagihan),
            'tgl_bayar' => 'required|date',
            'is_lunas' => 'required|boolean'
        ], $messages);

        if ($validator->fails()) {
            return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();

            $nominal_konsumsi = $request->nominal_konsumsi ?? $pembayaran->nominal_konsumsi;
            
            $total_tagihan = $pembayaran->nominal_spp + 
                            $nominal_konsumsi + 
                            $pembayaran->nominal_fullday +
                            $pembayaran->nominal_inklusi;
            
            $kembalian = $request->jumlah_bayar - $total_tagihan;
            $is_lunas = $kembalian >= 0; 
            $kembalian_lama = $pembayaran->kembalian; 

            $pembayaran->update([
                'bulan' => $request->bulan,
                'nominal_konsumsi' => $nominal_konsumsi,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tgl_bayar' => $request->tgl_bayar,
                'is_lunas' => $request->is_lunas,
                'kembalian' => $is_lunas ? $kembalian : 0,
            ]);

            $tabungan = Tabungan::where('id_pembayaran', $pembayaran->id)->first();
            
            if ($is_lunas && $kembalian > 0) {
                if ($tabungan) {
                    $tabungan->update([
                        'debit' => $kembalian,
                        'saldo' => $tabungan->saldo - $kembalian_lama + $kembalian,
                        'keterangan' => 'Kembalian pembayaran SPP bulan ' . ucfirst($request->bulan),
                    ]);
                } else {
                    $saldo_terakhir = Tabungan::where('id_siswa', $pembayaran->id_siswa)
                        ->latest()
                        ->first();
                    
                    $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
                    $saldo_sekarang = $saldo_sebelumnya + $kembalian;

                    Tabungan::create([
                        'id_siswa' => $pembayaran->id_siswa,
                        'id_pembayaran' => $pembayaran->id,
                        'id_petugas' => auth()->id(),
                        'debit' => $kembalian,
                        'kredit' => 0,
                        'saldo' => $saldo_sekarang,
                        'keterangan' => 'Kembalian pembayaran SPP bulan ' . ucfirst($request->bulan),
                    ]);
                }
            } elseif ($tabungan) {
                $tabungan->delete();
            }

            DB::commit();

            Alert::success('Berhasil!', 'Pembayaran berhasil diperbarui');
            return redirect()->route('entry-pembayaran.index');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating pembayaran: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat memperbarui pembayaran');
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            $pembayaran = Pembayaran::findOrFail($id);
            $tabungan = Tabungan::where('id_pembayaran', $pembayaran->id)->first();
            
            if ($tabungan) {
                $tabungan->delete();
            }
            
            $pembayaran->delete();
            
            DB::commit();
            Alert::success('Berhasil!', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            Alert::error('Gagal!', 'Terjadi kesalahan saat menghapus data');
        }
        
        return back();
    }

    public function generate($id)
    {
        try {
            $user = Auth::user();
            $tanggal = Carbon::now()->format('d-m-Y');
            $pembayaran = Pembayaran::with(['siswa', 'siswa.kelas', 'petugas'])->findOrFail($id);

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

            $pdf = PDF::loadView('pdf.bukti-pembayaran', compact(
                'pembayaran', 
                'logoData', 
                'websiteData', 
                'instagramData', 
                'facebookData', 
                'youtubeData', 
                'whatsappData', 
                'barcodeData', 
                'user',
                'tanggal'
            ))
            ->setPaper('a5', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'dpi' => 150,
            ]);

            $namaFile = 'Bukti-Pembayaran-SPP-' . $pembayaran->siswa->nama . '-' . $tanggal . '.pdf';
            
            return $pdf->download($namaFile);
            
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            Alert::error('Error', 'Gagal menghasilkan PDF: ' . $e->getMessage());
            return back();
        }
    }

    public function generateRiwayat($siswaId)
    {
        try {
            $siswa = Siswa::with(['kelas'])->findOrFail($siswaId);
            $riwayatPembayaran = Pembayaran::where('id_siswa', $siswaId)
                ->with('petugas')
                ->orderBy('tahun', 'desc')
                ->orderByRaw("FIELD(bulan, 'januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember') DESC")
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

            $pdf = PDF::loadView('pdf.rekap-pembayaran-spp', compact('siswa', 'riwayatPembayaran', 'logoData', 'tanggal', 'websiteData', 'instagramData', 'facebookData', 'youtubeData', 'whatsappData', 'barcodeData'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'dpi' => 150,
                ]);

            $namaFile = 'Rekap-Pembayaran-SPP-' . $siswa->nama . '-' . $tanggal . '.pdf';
            return $pdf->download($namaFile);
            
        } catch (\Exception $e) {
            Log::error('Error generating riwayat PDF: ' . $e->getMessage());
            Alert::error('Error', 'Gagal menghasilkan PDF riwayat: ' . $e->getMessage());
            return back();
        }
    }
}
