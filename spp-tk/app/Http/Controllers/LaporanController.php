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
    // Debug: Cek apakah method terpanggil
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
            // Statistik per Tahun - USING DB RAW
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
    
    // Method untuk mendapatkan nama bulan
    private function getNamaBulan($bulan) {
        $bulanArr = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return $bulanArr[$bulan] ?? 'Tidak Valid';
    }
}