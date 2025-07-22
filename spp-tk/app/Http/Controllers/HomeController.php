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
            // Current month payments (gunakan nama bulan lowercase)
            $currentPayments = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
                $query->where('id_kelas', $kelas->id);
            })
            ->where('bulan', strtolower($currentMonthName)) 
            ->where('tahun', $currentYear)
            ->sum('jumlah_bayar');

            // Previous month payments
            $previousPayments = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
                $query->where('id_kelas', $kelas->id);
            })
            ->where('bulan', strtolower($previousMonthName))
            ->where('tahun', $previousYear)
            ->sum('jumlah_bayar');

            // Unpaid students calculation
            $paidStudents = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
                $query->where('id_kelas', $kelas->id);
            })
            ->where('bulan', strtolower($currentMonthName))
            ->where('tahun', $currentYear)
            ->pluck('id_siswa');

            $unpaid = $kelas->siswa->whereNotIn('id', $paidStudents);
            
            $pemasukanSPPPerKelas[] = [ // Ubah menjadi array numerik
                'kelas' => $kelas->nama_kelas,
                'current' => $currentPayments,
                'previous' => $previousPayments,
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