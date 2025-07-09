<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Pembayaran;
use App\Models\Kelas;

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
    
    // Hitung total pemasukan bersih per kelas (jumlah_bayar - kembalian)
    $pemasukanPerKelas = [];
    foreach ($kelasList as $kelas) {
        $totalPembayaran = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
            $query->where('id_kelas', $kelas->id);
        })->sum('jumlah_bayar');
        
        $totalKembalian = Pembayaran::whereHas('siswa', function($query) use ($kelas) {
            $query->where('id_kelas', $kelas->id);
        })->sum('kembalian');
        
        $pemasukanPerKelas[$kelas->nama_kelas] = $totalPembayaran - $totalKembalian;
    }

    $data = [
        'user' => User::find(auth()->user()->id),
        'pembayaran' => Pembayaran::orderBy('id', 'desc')->paginate(5),
        'pemasukanPerKelas' => $pemasukanPerKelas,
    ];
  
    return view('dashboard.index', $data);
} 
}