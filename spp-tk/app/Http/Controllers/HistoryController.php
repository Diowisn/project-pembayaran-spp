<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alert;
use App\Models\Pembayaran;
use App\Models\AngsuranInfaq;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\UangTahunan;
use App\Models\SiswaKegiatan;
use App\Models\KegiatanTahunan;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'pembayaran' => Pembayaran::with(['siswa.kelas', 'siswa.spp', 'siswa.paketInklusi' ])
                            ->orderBy('created_at', 'DESC')
                            ->paginate(15),
            'user' => User::find(auth()->user()->id)
        ];
         
        return view('dashboard.history-pembayaran.index', $data);
    }

    /**
     * Display infaq payment history
     *
     * @return \Illuminate\Http\Response
     */
    public function infaq()
    {
        $data = [
            'infaqHistori' => AngsuranInfaq::with(['siswa.kelas', 'infaqGedung'])
                            ->orderBy('created_at', 'DESC')
                            ->paginate(15),
            'user' => User::find(auth()->user()->id),
            'kelasList' => Kelas::all()
        ];
        
        return view('dashboard.history-infaq.index', $data);
    }

    /**
     * Display annual fund payment history
     *
     * @return \Illuminate\Http\Response
     */
    public function uangTahunan(Request $request)
    {
        $query = UangTahunan::with(['siswa.kelas', 'petugas'])
                    ->orderBy('created_at', 'DESC');

        // Filter pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter tahun ajaran
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun_ajaran', $request->tahun);
        }

        $data = [
            'uangTahunanHistori' => $query->paginate(15),
            'tahunAjaran' => UangTahunan::select('tahun_ajaran')
                                ->distinct()
                                ->orderBy('tahun_ajaran', 'DESC')
                                ->pluck('tahun_ajaran'),
            'user' => User::find(auth()->user()->id),
            'search' => $request->search,
            'tahun' => $request->tahun
        ];
        
        return view('dashboard.history-uang-tahunan.index', $data);
    }

    /**
     * Display kegiatan payment history
     *
     * @return \Illuminate\Http\Response
     */
    public function kegiatan(Request $request)
    {
        $query = SiswaKegiatan::with(['siswa.kelas', 'kegiatan', 'petugas'])
                    ->where('partisipasi', 'ikut')
                    ->orderBy('created_at', 'DESC');

        // Filter pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter kegiatan
        if ($request->has('kegiatan') && $request->kegiatan != '') {
            $query->where('id_kegiatan', $request->kegiatan);
        }

        // Filter kelas
        if ($request->has('kelas') && $request->kelas != '') {
            $query->whereHas('siswa', function($q) use ($request) {
                $q->where('id_kelas', $request->kelas);
            });
        }

        // Filter tanggal
        if ($request->has('tanggal_mulai') && $request->has('tanggal_akhir')) {
            if ($request->tanggal_mulai && $request->tanggal_akhir) {
                $query->whereBetween('tgl_bayar', [
                    $request->tanggal_mulai,
                    $request->tanggal_akhir
                ]);
            }
        }

        $data = [
            'pembayaranKegiatan' => $query->paginate(15)->appends($request->all()),
            'user' => User::find(auth()->user()->id),
            'kegiatanList' => KegiatanTahunan::all(),
            'kelasList' => Kelas::all(),
            'search' => $request->search,
            'selectedKegiatan' => $request->kegiatan,
            'selectedKelas' => $request->kelas,
            'tanggalMulai' => $request->tanggal_mulai,
            'tanggalAkhir' => $request->tanggal_akhir
        ];
        
        return view('dashboard.history-kegiatan.index', $data);
    }

    /**
     * Display detail kegiatan payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showKegiatan($id)
    {
        $pembayaran = SiswaKegiatan::with(['siswa.kelas', 'kegiatan', 'petugas'])
                        ->where('partisipasi', 'ikut')
                        ->findOrFail($id);
        
        return view('dashboard.history-kegiatan.show', [
            'pembayaran' => $pembayaran,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    /**
     * Remove kegiatan payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyKegiatan($id)
    {
        try {
            $pembayaran = SiswaKegiatan::findOrFail($id);
            
            // Hapus data tabungan terkait jika ada
            $tabungan = \App\Models\Tabungan::where('id_pembayaran_kegiatan', $pembayaran->id)->first();
            if ($tabungan) {
                $tabungan->delete();
            }
            
            if($pembayaran->delete()) {
                // Update status lunas
                $this->updateLunasStatus($pembayaran->id_siswa, $pembayaran->id_kegiatan);
                
                Alert::success('Berhasil!', 'Pembayaran kegiatan berhasil dihapus!');
            } else {
                Alert::error('Terjadi Kesalahan!', 'Pembayaran kegiatan gagal dihapus!');
            }
            
        } catch (\Exception $e) {
            Alert::error('Terjadi Kesalahan!', 'Pembayaran kegiatan gagal dihapus!');
        }
        
        return back();
    }

    /**
     * Update lunas status for kegiatan
     */
    private function updateLunasStatus($idSiswa, $idKegiatan)
    {
        $totalDibayar = SiswaKegiatan::where('id_siswa', $idSiswa)
            ->where('id_kegiatan', $idKegiatan)
            ->sum('jumlah_bayar');
        
        $totalTagihan = KegiatanTahunan::find($idKegiatan)->nominal ?? 0;
        $isLunas = ($totalDibayar >= $totalTagihan);
        
        if ($isLunas) {
            SiswaKegiatan::where('id_siswa', $idSiswa)
                ->where('id_kegiatan', $idKegiatan)
                ->update(['is_lunas' => true]);
        } else {
            SiswaKegiatan::where('id_siswa', $idSiswa)
                ->where('id_kegiatan', $idKegiatan)
                ->update(['is_lunas' => false]);
        }
        
        return $isLunas;
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}