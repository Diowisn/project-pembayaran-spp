<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alert;
use App\Models\Pembayaran;
use App\Models\AngsuranInfaq;
use App\Models\Tabungan;
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
    public function index(Request $request)
    {
        $query = Pembayaran::withTrashed()
                    ->with(['siswa.kelas', 'siswa.spp', 'siswa.paketInklusi', 'petugas'])
                    ->orderBy('created_at', 'DESC');

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas') && !empty($request->kelas)) {
            $query->whereHas('siswa.kelas', function($q) use ($request) {
                $q->where('id', $request->kelas);
            });
        }

        // Filter berdasarkan bulan
        if ($request->has('bulan') && !empty($request->bulan)) {
            $query->where('bulan', $request->bulan);
        }

        // Filter berdasarkan tahun
        if ($request->has('tahun') && !empty($request->tahun)) {
            $query->where('tahun', $request->tahun);
        }

        // Filter berdasarkan status
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status == 'lunas') {
                $query->where('is_lunas', true);
            } elseif ($request->status == 'belum') {
                $query->where('is_lunas', false);
            }
        }

        $data = [
            'pembayaran' => $query->paginate(15),
            'user' => User::find(auth()->user()->id),
            'kelasList' => Kelas::all(),
        ];
         
        return view('dashboard.history-pembayaran.index', $data);
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with(['siswa.kelas', 'siswa.spp', 'siswa.paketInklusi', 'petugas'])->findOrFail($id);
        
        return view('dashboard.history-pembayaran.show', [
            'pembayaran' => $pembayaran,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    /**
     * Display savings transaction history
     */
    public function tabungan(Request $request)
    {
        $search = $request->input('search');
        $kelas = $request->input('kelas');
        
        $tabunganHistori = Tabungan::withTrashed()
            ->with(['siswa.kelas', 'petugas'])
            ->when($search, function($query) use ($search) {
                return $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->when($kelas, function($query) use ($kelas) {
                return $query->whereHas('siswa.kelas', function($q) use ($kelas) {
                    $q->where('id', $kelas);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.history-tabungan.index', [
            'tabunganHistori' => $tabunganHistori,
            'search' => $search,
            'kelas' => $kelas,
            'kelasList' => Kelas::all(),
            'user' => User::find(auth()->user()->id)
        ]);
    }

    /**
     * Display infaq payment history with filters
     *
     * @return \Illuminate\Http\Response
     */
    public function infaq(Request $request)
    {
        $query = AngsuranInfaq::withTrashed()
                    ->with(['siswa.kelas', 'infaqGedung', 'petugas'])
                    ->orderBy('created_at', 'DESC');

        // Filter berdasarkan pencarian
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan kelas
        if ($request->has('kelas') && !empty($request->kelas)) {
            $query->whereHas('siswa.kelas', function($q) use ($request) {
                $q->where('id', $request->kelas);
            });
        }

        // Filter berdasarkan status
        if ($request->has('status') && !empty($request->status)) {
            if ($request->status == 'lunas') {
                $query->where('is_lunas', true);
            } elseif ($request->status == 'belum') {
                $query->where('is_lunas', false);
            }
        }

        // Filter berdasarkan tahun
        if ($request->has('tahun') && !empty($request->tahun)) {
            $query->whereYear('tgl_bayar', $request->tahun);
        }

        $data = [
            'infaqHistori' => $query->paginate(15)->appends($request->all()),
            'user' => User::find(auth()->user()->id),
            'kelasList' => Kelas::all(),
            'search' => $request->search,
            'kelas' => $request->kelas,
            'status' => $request->status,
            'tahun' => $request->tahun
        ];
        
        return view('dashboard.history-infaq.index', $data);
    }

    /**
     * Show detail infaq payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showInfaq($id)
    {
        $pembayaran = AngsuranInfaq::with(['siswa.kelas', 'infaqGedung', 'petugas'])
                        ->findOrFail($id);
        
        // Hitung total pembayaran dan sisa
        $totalDibayar = AngsuranInfaq::where('id_siswa', $pembayaran->id_siswa)->sum('jumlah_bayar');
        $totalTagihan = $pembayaran->infaqGedung->nominal ?? 0;
        $sisaPembayaran = max(0, $totalTagihan - $totalDibayar);
        
        return view('dashboard.history-infaq.show', [
            'pembayaran' => $pembayaran,
            'total_dibayar' => $totalDibayar,
            'total_tagihan' => $totalTagihan,
            'sisa_pembayaran' => $sisaPembayaran,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    /**
     * Update lunas status for infaq
     */
    private function updateLunasStatusInfaq($idSiswa)
    {
        $totalDibayar = AngsuranInfaq::where('id_siswa', $idSiswa)->sum('jumlah_bayar');
        $siswa = Siswa::with('infaqGedung')->find($idSiswa);
        $totalTagihan = $siswa->infaqGedung->nominal ?? 0;
        $isLunas = ($totalDibayar >= $totalTagihan);
        
        AngsuranInfaq::where('id_siswa', $idSiswa)->update(['is_lunas' => $isLunas]);
        
        return $isLunas;
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
        $query = SiswaKegiatan::withTrashed()
                    ->with(['siswa.kelas', 'kegiatan', 'petugas'])
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

}