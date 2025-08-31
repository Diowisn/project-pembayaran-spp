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

        return view('dashboard.entri-pembayaran.index', [
            'pembayaran' => $query->paginate(10)->appends($request->all()),
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

        $siswa = Siswa::with('spp', 'paketInklusi')->where('nisn', $request->nisn)->first();

        return view('dashboard.entri-pembayaran.index', [
            'pembayaran' => Pembayaran::with(['petugas'])->orderBy('id', 'DESC')->paginate(10),
            'siswa' => $siswa,
            'user' => User::find(auth()->user()->id)
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
            'jumlah_tagihan' => 'required|numeric'
        ], [
            'bulan.required' => 'Bulan pembayaran harus dipilih',
            'min' => 'Pembayaran tidak boleh kurang dari :min',
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
            
            $nominal_inklusi = 0;
            if ($siswa->inklusi && $siswa->paketInklusi) {
                $nominal_inklusi = $siswa->paketInklusi->nominal;
            }

            $nominal_konsumsi = $request->nominal_konsumsi ?? $siswa->spp->nominal_konsumsi ?? 0;
            
            $total_tagihan = $siswa->spp->nominal_spp + 
                            // ($siswa->spp->nominal_konsumsi ?? 0) + 
                            $nominal_konsumsi +
                            ($siswa->spp->nominal_fullday ?? 0) +
                            $nominal_inklusi;

            $kembalian = $request->nominal_pembayaran - $total_tagihan;
            $is_lunas = $kembalian >= 0; 

            $pembayaran = Pembayaran::create([
                'id_petugas' => auth()->id(),
                'nama_petugas' => auth()->user()->name,
                'id_siswa' => $request->id_siswa,
                'id_spp' => $siswa->id_spp,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'nominal_spp' => $siswa->spp->nominal_spp,
                // 'nominal_konsumsi' => $siswa->spp->nominal_konsumsi ?? 0,
                'nominal_konsumsi' => $nominal_konsumsi,
                'nominal_fullday' => $siswa->spp->nominal_fullday ?? 0,
                'nominal_inklusi' => $nominal_inklusi,
                'jumlah_bayar' => $request->nominal_pembayaran,
                'kembalian' => $is_lunas ? $kembalian : 0,
                'tgl_bayar' => now(),
                'is_lunas' => $is_lunas, 
            ]);

            if ($is_lunas && $kembalian > 0) {
                $saldo_terakhir = Tabungan::where('id_siswa', $request->id_siswa)
                    ->latest()
                    ->first();
                
                $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
                $saldo_sekarang = $saldo_sebelumnya + $kembalian;

                Tabungan::create([
                    'id_siswa' => $request->id_siswa,
                    'id_pembayaran' => $pembayaran->id,
                    'id_petugas' => auth()->id(),
                    'debit' => $kembalian,
                    'kredit' => 0,
                    'saldo' => $saldo_sekarang,
                    'keterangan' => 'Kembalian pembayaran SPP bulan ' . ucfirst($request->bulan),
                ]);
            }

            DB::commit();

            Alert::success('Berhasil!', 'Pembayaran berhasil disimpan!');
            return redirect()->route('entry-pembayaran.index'); 

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating pembayaran: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat menyimpan pembayaran');
            return redirect()->route('pembayaran.cari-siswa', ['nisn' => $request->nisn])
                ->withInput();
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
        ini_set('max_execution_time', 300);
        
        $user = Auth::user();
        $tanggal = Carbon::now()->format('d-m-Y');
        $pembayaran = Pembayaran::with(['siswa', 'siswa.kelas', 'petugas'])->findOrFail($id);

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


        $pdf = PDF::loadView('pdf.bukti', compact('pembayaran', 'logoData', 'websiteData', 'instagramData', 'facebookData', 'youtubeData', 'whatsappData', 'barcodeData', 'user'))
                ->setPaper('a5', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'dpi' => 150,
                ]);

        $namaFile = 'Bukti-Pembayaran-SPP-' . $pembayaran->siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }
}
