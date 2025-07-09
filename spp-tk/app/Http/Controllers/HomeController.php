<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Pembayaran;
use App\Models\Kelas;
use App\Models\AngsuranInfaq;
use App\Models\Siswa;

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
    // Ambil semua kelas
    $kelasList = Kelas::all();
    
    // Hitung total pemasukan bersih SPP per kelas (jumlah_bayar - kembalian)
    $pemasukanSPPPerKelas = [];
    foreach ($kelasList as $kelas) {
        $totalPembayaran = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
            $query->where('id_kelas', $kelas->id);
        })->sum('jumlah_bayar');
        
        $totalKembalian = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
            $query->where('id_kelas', $kelas->id);
        })->sum('kembalian');
        
        $pemasukanSPPPerKelas[$kelas->nama_kelas] = $totalPembayaran - $totalKembalian;
    }

    // Hitung total pemasukan Infaq Gedung per kelas
    $pemasukanInfaqPerKelas = [];
    foreach ($kelasList as $kelas) {
        $totalInfaq = AngsuranInfaq::whereHas('siswa', function($query) use ($kelas) {
            $query->where('id_kelas', $kelas->id);
        })->sum('jumlah_bayar');
        
        $pemasukanInfaqPerKelas[$kelas->nama_kelas] = $totalInfaq;
    }

    // Hitung total semua pemasukan
    $totalPemasukanSPP = array_sum($pemasukanSPPPerKelas);
    $totalPemasukanInfaq = array_sum($pemasukanInfaqPerKelas);
    $totalSemuaPemasukan = $totalPemasukanSPP + $totalPemasukanInfaq;

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
        'pemasukanPerKelas' => $pemasukanSPPPerKelas, // Keep this for backward compatibility
        'pemasukanSPPPerKelas' => $pemasukanSPPPerKelas,
        'pemasukanInfaqPerKelas' => $pemasukanInfaqPerKelas,
        'totalPemasukanSPP' => $totalPemasukanSPP,
        'totalPemasukanInfaq' => $totalPemasukanInfaq,
        'totalSemuaPemasukan' => $totalSemuaPemasukan,
        'jumlahSiswa' => Siswa::count(),
        'jumlahPembayaranSPP' => Pembayaran::count(),
        'jumlahPembayaranInfaq' => AngsuranInfaq::count(),
    ];
  
    return view('dashboard.index', $data);
}
}