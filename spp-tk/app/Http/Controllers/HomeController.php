<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Pembayaran;
use App\Models\Kelas;
use App\Models\AngsuranInfaq;
use App\Models\Siswa;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $bulanIndo = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');
        $currentMonthName = $bulanIndo[now()->format('F')];
        $previousMonth = now()->subMonth()->format('m');
        $previousYear = now()->subMonth()->format('Y');
        $previousMonthName = $bulanIndo[now()->subMonth()->format('F')];

        // Get all classes
        $kelasList = Kelas::with(['siswa'])->get();
        
        // Payment statistics
        $pemasukanSPPPerKelas = [];

        foreach ($kelasList as $kelas) {
                $targetSPP = 0;
                $targetKonsumsi = 0;
                $targetFullday = 0;
                
                foreach ($kelas->siswa as $siswa) {
                    $targetSPP += $siswa->spp->nominal_spp ?? 0;
                    $targetKonsumsi += $siswa->spp->nominal_konsumsi ?? 0;
                    $targetFullday += $siswa->spp->nominal_fullday ?? 0;
                }
                
                $targetPenerimaan = $targetSPP + $targetKonsumsi + $targetFullday;
            // Current month payments (gunakan nama bulan lowercase)
            $currentPayments = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
                $query->where('id_kelas', $kelas->id);
            })
            ->where('bulan', strtolower($currentMonthName))
            ->where('tahun', $currentYear)
            ->get() // Ubah dari sum() ke get() untuk menghitung net payment
            ->sum(function($item) {
                return $item->jumlah_bayar - $item->kembalian; // Hitung net payment
            });

            // Previous month payments
            $previousPayments = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
                $query->where('id_kelas', $kelas->id);
            })
            ->where('bulan', strtolower($previousMonthName))
            ->where('tahun', $previousYear)
            ->get()
            ->sum(function($item) {
                return $item->jumlah_bayar - $item->kembalian;
            });

            // Unpaid students calculation
            $paidStudents = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
                $query->where('id_kelas', $kelas->id);
            })
            ->where('bulan', strtolower($currentMonthName))
            ->where('tahun', $currentYear)
            ->pluck('id_siswa');

            $unpaid = $kelas->siswa->whereNotIn('id', $paidStudents);
            
            $pemasukanSPPPerKelas[] = [
                'kelas' => $kelas->nama_kelas,
                'current' => $currentPayments,
                'previous' => $previousPayments,
                'target_penerimaan' => $targetPenerimaan,
                'target_spp' => $targetSPP,
                'target_konsumsi' => $targetKonsumsi,
                'target_fullday' => $targetFullday,
                'unpaid_count' => $unpaid->count(),
                'unpaid_students' => $unpaid,
                'total_students' => $kelas->siswa->count(),
                'payment_rate' => $kelas->siswa->count() > 0 ? 
                    round(($kelas->siswa->count() - $unpaid->count()) / $kelas->siswa->count() * 100, 2) : 0
            ];
        }

        $data = [
                'user' => User::find(auth()->user()->id),
                'pembayaran' => Pembayaran::with(['siswa.kelas', 'siswa.spp'])
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get(),
                'infaqHistori' => AngsuranInfaq::with(['siswa.kelas', 'infaqGedung'])
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get(),
                'pemasukanSPPPerKelas' => $pemasukanSPPPerKelas,
                'currentMonthName' => $currentMonthName,
                'previousMonthName' => $previousMonthName,
                'kelasList' => $kelasList
            ];
        
        return view('dashboard.index', [
            'user' => User::find(auth()->user()->id),
            'pembayaran' => Pembayaran::with(['siswa.kelas', 'siswa.spp'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get(),
            'infaqHistori' => AngsuranInfaq::with(['siswa.kelas', 'infaqGedung'])
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get(),
            'pemasukanSPPPerKelas' => $pemasukanSPPPerKelas,
            'currentMonthName' => $currentMonthName,
            'previousMonthName' => $previousMonthName,
            'kelasList' => $kelasList
        ]);
    }
}