<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App;
use PDF;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function __construct(){
        $this->middleware([
            'auth',
            'privilege:admin'
        ]);
    }
   
    // Halaman utama laporan (menu pilihan)
    public function index(){
        $data = [
            'user' => User::find(auth()->user()->id),
        ];
      
        return view('dashboard.generate-laporan.index', $data);
    }
    
    // Halaman laporan per kelas
    public function laporanKelas(){
        $data = [
            'user' => User::find(auth()->user()->id),
            'kelas' => Kelas::orderBy('nama_kelas', 'ASC')->get(),
            'tahun' => date('Y'),
        ];
      
        return view('dashboard.generate-laporan.laporan-kelas', $data);
    }
    
    // Halaman laporan per bulan
    public function laporanBulan(){
        $data = [
            'user' => User::find(auth()->user()->id),
            'tahun' => date('Y'),
        ];
      
        return view('dashboard.generate-laporan.laporan-bulan', $data);
    }
    
    // Halaman laporan per semester
    public function laporanSemester(){
        $data = [
            'user' => User::find(auth()->user()->id),
            'tahun' => date('Y'),
        ];
      
        return view('dashboard.generate-laporan.laporan-semester', $data);
    }
    
    // Halaman laporan per tahun
    public function laporanTahun(){
        $data = [
            'user' => User::find(auth()->user()->id),
            'tahun' => date('Y'),
        ];
      
        return view('dashboard.generate-laporan.laporan-tahun', $data);
    }
    
    // Halaman laporan semua data
    public function laporanSemua(){
        $data = [
            'user' => User::find(auth()->user()->id),
        ];
      
        return view('dashboard.generate-laporan.laporan-semua', $data);
    }
   
    public function create(Request $request){
        \Log::info('LaporanController create method called', ['request' => $request->all()]);
        
        PDF::setOptions([
            'dpi' => 150, 
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ]);
        
        // Validasi input
        $request->validate([
            'jenis_laporan' => 'required|in:semua,per_bulan,per_semester,per_tahun,per_kelas',
            'kelas_id' => 'nullable|array',
            'kelas_id.*' => 'exists:kelas,id',
            'bulan' => 'nullable|array',
            'bulan.*' => 'integer|between:1,12',
            'tahun' => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
            'semester' => 'nullable|integer|between:1,2',
            'tahun_laporan' => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
        ]);
        
        // Query dasar
        $query = Pembayaran::with(['petugas', 'siswa.kelas', 'siswa.spp'])
            ->orderBy('tgl_bayar', 'DESC')
            ->orderBy('created_at', 'DESC');
        
        // Filter berdasarkan jenis laporan
        $jenisLaporan = $request->jenis_laporan;
        $filterText = '';
        
        switch ($jenisLaporan) {
            case 'per_bulan':
                $tahun = $request->tahun;
                $bulanMultiple = $request->bulan ?? [];
                
                if (!empty($bulanMultiple)) {
                    $query->where(function($q) use ($bulanMultiple, $tahun) {
                        foreach ($bulanMultiple as $bulan) {
                            $q->orWhere(function($q2) use ($bulan, $tahun) {
                                $q2->whereMonth('tgl_bayar', $bulan)
                                ->whereYear('tgl_bayar', $tahun);
                            });
                        }
                    });
                    
                    $namaBulan = array_map([$this, 'getNamaBulan'], $bulanMultiple);
                    $filterText = "Bulan: " . implode(', ', $namaBulan) . " Tahun: " . $tahun;
                } else {
                    $query->whereYear('tgl_bayar', $tahun);
                    $filterText = "Semua Bulan Tahun: " . $tahun;
                }
                break;
                
            case 'per_semester':
                $semester = $request->semester;
                $tahun = $request->tahun;
                
                if ($semester == 1) {
                    $query->whereBetween('tgl_bayar', [
                        $tahun . '-01-01',
                        $tahun . '-06-30'
                    ]);
                    $filterText = "Semester 1 (Januari - Juni) Tahun: " . $tahun;
                } else {
                    $query->whereBetween('tgl_bayar', [
                        $tahun . '-07-01',
                        $tahun . '-12-31'
                    ]);
                    $filterText = "Semester 2 (Juli - Desember) Tahun: " . $tahun;
                }
                break;
                
            case 'per_tahun':
                $tahun = $request->tahun_laporan ?? $request->tahun ?? date('Y');
                $query->whereYear('tgl_bayar', $tahun);
                $filterText = "Tahun: " . $tahun;
                break;
                
            case 'per_kelas':
                $kelasId = $request->kelas_id ?? [];
                $tahun = $request->tahun_laporan ?? $request->tahun ?? date('Y');
                
                if (!empty($kelasId)) {
                    $query->whereHas('siswa', function($q) use ($kelasId) {
                        $q->whereIn('id_kelas', $kelasId);
                    });
                    
                    $kelasNames = Kelas::whereIn('id', $kelasId)->pluck('nama_kelas')->toArray();
                    $filterText = "Kelas: " . implode(', ', $kelasNames) . " Tahun: " . $tahun;
                } else {
                    $filterText = "Semua Kelas Tahun: " . $tahun;
                }
                
                $query->whereYear('tgl_bayar', $tahun);
                break;
                
            default: // semua
                $filterText = "Semua Data (Semua Tahun)";
                // Tidak ada filter tambahan untuk semua data
                break;
        }
        
        $pembayaran = $query->get();

        // Debug: Cek jumlah data
        \Log::info('Data retrieved for report', [
            'jenis_laporan' => $jenisLaporan,
            'count' => $pembayaran->count(),
            'filter' => $filterText
        ]);
        
        // Hitung total TERLEBIH DAHULU sebelum statistik
        $totalPembayaran = $pembayaran->sum('jumlah_bayar');
        $totalSpp = $pembayaran->sum('nominal_spp');
        $totalKonsumsi = $pembayaran->sum('nominal_konsumsi');
        $totalFullday = $pembayaran->sum('nominal_fullday');
        $totalInklusi = $pembayaran->sum('nominal_inklusi');

        // HITUNG STATISTIK UNTUK LAPORAN SEMUA DATA - SETELAH totalPembayaran didefinisikan
        $statistikTahunan = [];
        $statistikKelas = [];
        $statistikBulanan = [];
        $jumlahSiswaUnik = 0;
        $jumlahKelasUnik = 0;
        $tahunAwal = date('Y');
        $tahunAkhir = date('Y');
        $rataTransaksiPerSiswa = 0;
        $rataPembayaranPerSiswa = 0;
        $bulanTertinggi = null;
        $bulanTerendah = null;
        $tahunTertinggi = null;
        $tahunTerendah = null;

        if ($jenisLaporan == 'semua') {
            try {
                $tahunData = \DB::select("
                    SELECT YEAR(tgl_bayar) as tahun, 
                        COUNT(*) as total_transaksi, 
                        SUM(jumlah_bayar) as total_pembayaran 
                    FROM pembayaran 
                    WHERE deleted_at IS NULL 
                    GROUP BY YEAR(tgl_bayar) 
                    ORDER BY tahun DESC
                ");
                
                foreach ($tahunData as $tahun) {
                    $statistikTahunan[$tahun->tahun] = [
                        'transaksi' => $tahun->total_transaksi,
                        'total' => $tahun->total_pembayaran,
                        'percentage' => $totalPembayaran > 0 ? ($tahun->total_pembayaran / $totalPembayaran) * 100 : 0
                    ];
                }
                
                // Statistik per Kelas - USING DB RAW
                $kelasData = \DB::select("
                    SELECT k.nama_kelas, 
                        COUNT(*) as total_transaksi, 
                        SUM(p.jumlah_bayar) as total_pembayaran 
                    FROM pembayaran p
                    JOIN siswa s ON p.id_siswa = s.id 
                    JOIN kelas k ON s.id_kelas = k.id 
                    WHERE p.deleted_at IS NULL 
                    GROUP BY k.id, k.nama_kelas 
                    ORDER BY total_pembayaran DESC
                ");
                
                foreach ($kelasData as $kelas) {
                    $statistikKelas[$kelas->nama_kelas] = [
                        'transaksi' => $kelas->total_transaksi,
                        'total' => $kelas->total_pembayaran,
                        'percentage' => $totalPembayaran > 0 ? ($kelas->total_pembayaran / $totalPembayaran) * 100 : 0
                    ];
                }
                
                // Statistik per Bulan - USING DB RAW
                $bulanData = \DB::select("
                    SELECT MONTH(tgl_bayar) as bulan, 
                        COUNT(*) as total_transaksi, 
                        SUM(jumlah_bayar) as total_pembayaran 
                    FROM pembayaran 
                    WHERE deleted_at IS NULL 
                    GROUP BY MONTH(tgl_bayar) 
                    ORDER BY bulan ASC
                ");
                
                $allMonths = [];
                for ($i = 1; $i <= 12; $i++) {
                    $allMonths[$i] = [
                        'count' => 0,
                        'total' => 0,
                        'percentage' => 0
                    ];
                }
                
                foreach ($bulanData as $bulan) {
                    $allMonths[$bulan->bulan]['count'] = $bulan->total_transaksi;
                    $allMonths[$bulan->bulan]['total'] = $bulan->total_pembayaran;
                    $allMonths[$bulan->bulan]['percentage'] = $totalPembayaran > 0 ? ($bulan->total_pembayaran / $totalPembayaran) * 100 : 0;
                }
                
                $statistikBulanan = $allMonths;
                
                // Hitung statistik tambahan
                $jumlahSiswaUnik = $pembayaran->unique('id_siswa')->count();
                $jumlahKelasUnik = $pembayaran->unique('siswa.id_kelas')->count();
                
                // Tahun awal dan akhir - USING DB RAW
                $tahunRange = \DB::selectOne("
                    SELECT YEAR(MIN(tgl_bayar)) as tahun_awal, 
                        YEAR(MAX(tgl_bayar)) as tahun_akhir 
                    FROM pembayaran 
                    WHERE deleted_at IS NULL
                ");
                
                $tahunAwal = $tahunRange->tahun_awal ?: date('Y');
                $tahunAkhir = $tahunRange->tahun_akhir ?: date('Y');
                
                $rataTransaksiPerSiswa = $jumlahSiswaUnik > 0 ? $pembayaran->count() / $jumlahSiswaUnik : 0;
                $rataPembayaranPerSiswa = $jumlahSiswaUnik > 0 ? $totalPembayaran / $jumlahSiswaUnik : 0;
                
                // Bulan dengan performa terbaik/terburuk
                $bulanTertinggi = collect($allMonths)->sortByDesc('total')->first();
                $bulanTerendah = collect($allMonths)->where('total', '>', 0)->sortBy('total')->first();
                
                // Tahun dengan performa terbaik/terburuk
                $tahunTertinggi = collect($tahunData)->sortByDesc('total_pembayaran')->first();
                $tahunTerendah = collect($tahunData)->where('total_pembayaran', '>', 0)->sortBy('total_pembayaran')->first();
                
            } catch (\Exception $e) {
                \Log::error('Error calculating statistics: ' . $e->getMessage());
                // Tetap lanjut tanpa statistik jika ada error
            }
        }
        
        // TAMBAH DATA TANGGAL & PETUGAS UNTUK LAPORAN REGULER
        $tanggalSurat = now();
        $namaPetugas = auth()->user()->name;
        
        $data = [
            'pembayaran' => $pembayaran,
            'filter_text' => $filterText,
            'jenis_laporan' => $jenisLaporan,
            'total_pembayaran' => $totalPembayaran,
            'total_spp' => $totalSpp,
            'total_konsumsi' => $totalKonsumsi,
            'total_fullday' => $totalFullday,
            'total_inklusi' => $totalInklusi,
            'tanggal_cetak' => now()->format('d F Y H:i:s'),
            'tanggal_surat' => $tanggalSurat, 
            'nama_petugas' => $namaPetugas,
            
            // Data statistik untuk laporan semua data
            'statistik_tahunan' => $statistikTahunan,
            'statistik_kelas' => $statistikKelas,
            'statistik_bulanan' => $statistikBulanan,
            'jumlah_siswa_unik' => $jumlahSiswaUnik,
            'jumlah_kelas_unik' => $jumlahKelasUnik,
            'tahun_awal' => $tahunAwal,
            'tahun_akhir' => $tahunAkhir,
            'rata_transaksi_per_siswa' => $rataTransaksiPerSiswa,
            'rata_pembayaran_per_siswa' => $rataPembayaranPerSiswa,
            'bulan_tertinggi' => $bulanTertinggi,
            'bulan_terendah' => $bulanTerendah,
            'tahun_tertinggi' => $tahunTertinggi,
            'tahun_terendah' => $tahunTerendah,
        ];

        // Pass the getNamaBulan function to the view
        $data['getNamaBulan'] = function($bulan) {
            return $this->getNamaBulan($bulan);
        };

        try {
            $pdf = PDF::loadView('pdf.laporan', $data);
            
            $filename = 'Laporan-pembayaran-spp-' . str_replace(' ', '-', strtolower($filterText)) . '-' . date('Y-m-d') . '.pdf';
            
            // Debug: Log sebelum download
            \Log::info('PDF generated successfully', ['filename' => $filename]);
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            
            // Fallback: Redirect back dengan error message
            return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }
    
    private function getNamaBulan($bulan) {
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanArr[$bulan] ?? 'Tidak Valid';
    }

    public function getSiswaBelumBayar(Request $request){
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|array',
            'bulan.*' => 'integer|between:1,12',
            'kelas_id' => 'nullable|array',
            'kelas_id.*' => 'exists:kelas,id',
        ]);

        $tahun = $request->tahun;
        $bulanDipilih = $request->bulan;
        $kelasId = $request->kelas_id ?? [];

        // Query siswa yang aktif
        $query = Siswa::with(['kelas', 'spp'])
            ->whereHas('kelas')
            ->whereHas('spp');

        // Filter kelas jika dipilih
        if (!empty($kelasId)) {
            $query->whereIn('id_kelas', $kelasId);
        }

        $semuaSiswa = $query->get();

        // Ambil semua pembayaran untuk bulan dan tahun yang dipilih
        $pembayaran = Pembayaran::whereIn('bulan', $bulanDipilih)
            ->where('tahun', $tahun)
            ->get();

        // Identifikasi siswa yang belum bayar
        $siswaBelumBayar = [];
        
        foreach ($semuaSiswa as $siswa) {
            $tunggakan = [];
            
            foreach ($bulanDipilih as $bulan) {
                $sudahBayar = $pembayaran->where('id_siswa', $siswa->id)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();

                if (!$sudahBayar) {
                    $tunggakan[] = [
                        'bulan' => $bulan,
                        'nama_bulan' => $this->getNamaBulan($bulan),
                        'nominal_spp' => $siswa->spp->nominal ?? 0,
                    ];
                }
            }

            if (!empty($tunggakan)) {
                $siswaBelumBayar[] = [
                    'siswa' => $siswa,
                    'tunggakan' => $tunggakan,
                    'total_tunggakan' => collect($tunggakan)->sum('nominal_spp'),
                    'jumlah_bulan_tunggakan' => count($tunggakan),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $siswaBelumBayar,
            'total_siswa' => count($siswaBelumBayar),
            'total_tunggakan' => collect($siswaBelumBayar)->sum('total_tunggakan'),
            'filter' => [
                'tahun' => $tahun,
                'bulan' => $bulanDipilih,
                'nama_bulan' => array_map([$this, 'getNamaBulan'], $bulanDipilih),
                'kelas_id' => $kelasId,
                'kelas_selected' => !empty($kelasId) ? 
                    Kelas::whereIn('id', $kelasId)->pluck('nama_kelas')->toArray() : null,
            ]
        ]);
    }

    public function laporanTunggakan(Request $request){
        $tahun = $request->tahun ?? date('Y');
        $bulanSekarang = date('n');
        
        // Handle bulan - PASTIKAN UNIK dan valid
        if ($request->has('bulan')) {
            $bulanDipilih = is_array($request->bulan) ? $request->bulan : [$request->bulan];
            $bulanDipilih = array_unique($bulanDipilih);
            $bulanDipilih = array_filter($bulanDipilih, function($bulan) {
                return $bulan >= 1 && $bulan <= 12;
            });
        } else {
            $bulanDipilih = range(1, $bulanSekarang);
        }
        
        // Handle kelas_id - PASTIKAN UNIK
        $kelasId = [];
        if ($request->has('kelas_id')) {
            $kelasId = is_array($request->kelas_id) ? $request->kelas_id : [$request->kelas_id];
            $kelasId = array_unique($kelasId);
            $kelasId = array_filter($kelasId);
        }
        
        \Log::info('laporanTunggakan - Parameters cleaned:', [
            'tahun' => $tahun,
            'bulanDipilih' => $bulanDipilih,
            'kelasId' => $kelasId
        ]);

        // Ambil data tunggakan
        $siswaBelumBayar = $this->getDataTunggakan($tahun, $bulanDipilih, $kelasId);

        $data = [
            'user' => User::find(auth()->user()->id),
            'kelas' => Kelas::orderBy('nama_kelas', 'ASC')->get(),
            'tahun' => $tahun,
            'bulan' => $bulanSekarang,
            'siswa_belum_bayar' => $siswaBelumBayar,
            'total_siswa' => count($siswaBelumBayar),
            'total_tunggakan' => collect($siswaBelumBayar)->sum('total_tunggakan'),
            'bulan_dipilih' => $bulanDipilih,
            'filter_kelas' => $kelasId,
        ];
    
        return view('dashboard.generate-laporan.laporan-tunggakan', $data);
    }

    public function createLaporanTunggakan(Request $request){
        \Log::info('createLaporanTunggakan called', ['request' => $request->all()]);
        
        // Validasi input
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|array',
            'bulan.*' => 'integer|between:1,12',
            'kelas_id' => 'nullable|array',
            'kelas_id.*' => 'exists:kelas,id',
        ]);

        $tahun = $request->tahun;
        
        // CLEAN INPUT - pastikan unique
        $bulanDipilih = array_unique($request->bulan);
        $kelasId = !empty($request->kelas_id) ? array_unique($request->kelas_id) : [];

        \Log::info('PDF Generation - Cleaned input:', [
            'tahun' => $tahun,
            'bulanDipilih' => $bulanDipilih,
            'kelasId' => $kelasId
        ]);

        // Ambil data tunggakan
        $siswaBelumBayar = $this->getDataTunggakan($tahun, $bulanDipilih, $kelasId);

        // Buat teks filter
        $namaBulan = array_map([$this, 'getNamaBulan'], $bulanDipilih);
        
        $filterKelas = "Semua Kelas";
        if (!empty($kelasId)) {
            $kelasNames = Kelas::whereIn('id', $kelasId)->pluck('nama_kelas')->toArray();
            $filterKelas = "Kelas: " . implode(', ', $kelasNames);
        }

        $filterText = "Laporan Tunggakan SPP - Tahun: {$tahun}, Bulan: " . implode(', ', $namaBulan) . ", {$filterKelas}";

        // TAMBAH DATA TANGGAL & PETUGAS
        $tanggalSurat = now();
        $namaPetugas = auth()->user()->name;

        $data = [
            'siswa_belum_bayar' => $siswaBelumBayar,
            'filter_text' => $filterText,
            'tanggal_cetak' => now()->format('d F Y H:i:s'),
            'tanggal_surat' => $tanggalSurat, 
            'nama_petugas' => $namaPetugas,
            'total_siswa' => count($siswaBelumBayar),
            'total_tunggakan' => collect($siswaBelumBayar)->sum('total_tunggakan'),
            'getNamaBulan' => function($bulan) {
                return $this->getNamaBulan($bulan);
            },
        ];

        \Log::info('PDF Data prepared', [
            'total_siswa' => count($siswaBelumBayar),
            'total_tunggakan' => collect($siswaBelumBayar)->sum('total_tunggakan'),
            'nama_petugas' => $namaPetugas
        ]);

        try {
            // Konfigurasi PDF
            PDF::setOptions([
                'dpi' => 150, 
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'isPhpEnabled' => true,
                'chroot' => public_path(),
                'logOutputFile' => storage_path('logs/dompdf.log'),
                'tempDir' => storage_path('temp/')
            ]);

            $pdf = PDF::loadView('pdf.laporan-tunggakan', $data);
            
            $filename = 'Laporan-Tunggakan-SPP-' . date('Y-m-d-H-i-s') . '.pdf';
            
            \Log::info('PDF generated successfully, attempting download', ['filename' => $filename]);
            
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());
            \Log::error('PDF Stack Trace: ' . $e->getTraceAsString());
            
            return redirect()->back()->with('error', 'Gagal generate PDF: ' . $e->getMessage());
        }
    }

    public function filterTunggakan(Request $request){
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|array',
            'bulan.*' => 'integer|between:1,12',
            'kelas_id' => 'nullable|array',
            'kelas_id.*' => 'exists:kelas,id',
        ]);

        $tahun = $request->tahun;
        
        // CLEAN INPUT - pastikan unique
        $bulanDipilih = array_unique($request->bulan);
        $kelasId = !empty($request->kelas_id) ? array_unique($request->kelas_id) : [];

        \Log::info('filterTunggakan - Cleaned input:', [
            'bulanDipilih' => $bulanDipilih,
            'kelasId' => $kelasId
        ]);

        $siswaBelumBayar = $this->getDataTunggakan($tahun, $bulanDipilih, $kelasId);

        return response()->json([
            'success' => true,
            'data' => $siswaBelumBayar,
            'total_siswa' => count($siswaBelumBayar),
            'total_tunggakan' => collect($siswaBelumBayar)->sum('total_tunggakan'),
            'filter' => [
                'tahun' => $tahun,
                'bulan' => $bulanDipilih,
                'nama_bulan' => array_map([$this, 'getNamaBulan'], $bulanDipilih),
                'kelas_id' => $kelasId,
                'kelas_selected' => !empty($kelasId) ? 
                    Kelas::whereIn('id', $kelasId)->pluck('nama_kelas')->toArray() : null,
            ]
        ]);
    }

    private function getDataTunggakan($tahun, $bulanDipilih, $kelasId) {
        \Log::info('=== START getDataTunggakan ===', [
            'tahun' => $tahun,
            'bulanDipilih' => $bulanDipilih,
            'kelasId' => $kelasId
        ]);

        // Query siswa yang aktif
        $query = Siswa::with(['kelas', 'spp'])
            ->whereHas('kelas')
            ->whereHas('spp');

        // Filter kelas jika dipilih - PASTIKAN UNIK
        if (!empty($kelasId)) {
            $kelasId = array_unique($kelasId);
            $query->whereIn('id_kelas', $kelasId);
        }

        $semuaSiswa = $query->get();

        \Log::info('Siswa aktif ditemukan', [
            'count' => $semuaSiswa->count(),
            'siswa' => $semuaSiswa->pluck('nama')->toArray()
        ]);

        // Map bulan angka ke nama bulan lowercase
        $bulanMap = [
            1 => 'januari', 2 => 'februari', 3 => 'maret', 4 => 'april',
            5 => 'mei', 6 => 'juni', 7 => 'juli', 8 => 'agustus',
            9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember'
        ];

        // Konversi bulan yang dipilih ke format lowercase
        $bulanDipilihLower = [];
        foreach ($bulanDipilih as $bulanAngka) {
            if (isset($bulanMap[$bulanAngka])) {
                $bulanDipilihLower[] = $bulanMap[$bulanAngka];
            }
        }

        \Log::info('Bulan filter', [
            'angka' => $bulanDipilih,
            'lowercase' => $bulanDipilihLower
        ]);

        // DEBUG: Cek data pembayaran di database
        $debugPembayaran = Pembayaran::where('tahun', $tahun)
            ->whereIn('bulan', $bulanDipilihLower)
            ->get();

        \Log::info('Data pembayaran di database untuk filter ini:', [
            'total' => $debugPembayaran->count(),
            'detail' => $debugPembayaran->map(function($item) {
                return [
                    'id_siswa' => $item->id_siswa,
                    'bulan' => $item->bulan,
                    'tahun' => $item->tahun,
                    'is_lunas' => $item->is_lunas
                ];
            })->toArray()
        ]);

        // Ambil pembayaran untuk filter
        $semuaPembayaran = Pembayaran::where('tahun', $tahun)
            ->where('is_lunas', true)
            ->whereIn('bulan', $bulanDipilihLower)
            ->get()
            ->groupBy('id_siswa');

        \Log::info('Pembayaran lunas yang ditemukan:', [
            'count' => $semuaPembayaran->count(),
            'per_siswa' => $semuaPembayaran->map(function($item) {
                return $item->pluck('bulan')->toArray();
            })->toArray()
        ]);

        // Identifikasi siswa yang belum bayar
        $siswaBelumBayar = [];
        
        foreach ($semuaSiswa as $siswa) {
            $tunggakan = [];
            
            // Ambil pembayaran siswa ini
            $pembayaranSiswa = $semuaPembayaran->get($siswa->id, collect());
            $bulanSudahBayar = $pembayaranSiswa->pluck('bulan')->toArray();

            \Log::info("Cek siswa: {$siswa->nama}", [
                'id_siswa' => $siswa->id,
                'bulan_sudah_bayar' => $bulanSudahBayar,
                'bulan_dicek' => $bulanDipilihLower
            ]);
            
            foreach ($bulanDipilih as $bulanAngka) {
                $bulanNama = $bulanMap[$bulanAngka] ?? '';
                
                if (empty($bulanNama)) continue;
                
                // Cek apakah bulan ini belum dibayar
                if (!in_array($bulanNama, $bulanSudahBayar)) {
                    $tunggakan[] = [
                        'bulan' => $bulanAngka,
                        'nama_bulan' => $this->getNamaBulan($bulanAngka),
                        'nama_bulan_lower' => $bulanNama,
                        'nominal_spp' => $siswa->spp->nominal_spp ?? 0,
                    ];
                }
            }

            // Hanya tambahkan jika ada tunggakan
            if (!empty($tunggakan)) {
                $siswaBelumBayar[] = [
                    'siswa' => $siswa,
                    'tunggakan' => $tunggakan,
                    'total_tunggakan' => collect($tunggakan)->sum('nominal_spp'),
                    'jumlah_bulan_tunggakan' => count($tunggakan),
                ];
                
                \Log::info("✅ Siswa dengan tunggakan: {$siswa->nama}", [
                    'tunggakan_bulan' => collect($tunggakan)->pluck('nama_bulan')->toArray(),
                    'total_tunggakan' => collect($tunggakan)->sum('nominal_spp')
                ]);
            }
        }

        \Log::info('=== FINISH getDataTunggakan ===', [
            'total_siswa_tunggakan' => count($siswaBelumBayar),
            'siswa_tunggakan' => collect($siswaBelumBayar)->pluck('siswa.nama')->toArray()
        ]);

        return $siswaBelumBayar;
    }
}