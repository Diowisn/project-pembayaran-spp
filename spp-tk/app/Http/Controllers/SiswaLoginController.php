<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alert;
use Session;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\AngsuranInfaq;
use Illuminate\Support\Facades\Hash;

class SiswaLoginController extends Controller
{
    public function siswaLogin()
    {
        if (session('nisn') != null) {  
            return redirect('dashboard/siswa/histori');
        }
    
        return view('auth.siswa-login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $siswa = Siswa::where('nisn', $request->nisn)->first();
        
        if ($siswa) {
            // Verifikasi password
            if (Hash::check($request->password, $siswa->password)) {
                Session::put('id', $siswa->id);
                Session::put('nama', $siswa->nama);
                Session::put('nisn', $siswa->nisn);
                
                return redirect('dashboard/siswa/histori');
            } else {
                Alert::error('Gagal Login!', 'Password salah');
                return back();
            }
        } else {
            Alert::error('Gagal Login!', 'Data siswa dengan NISN ini tidak ditemukan');
            return back();
        }
    }
    
    public function logout()
    {
        Session::flush();
        return redirect('login/siswa');
    }
    
    public function index()
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $data = [
            'pembayaran' => Pembayaran::where('id_siswa', Session::get('id'))->paginate(10)
        ];
        
        return view('dashboard.siswa.index', $data);
    }

    public function infaq()
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $siswa = Siswa::find(Session::get('id'));
        
        $data = [
            'infaqHistori' => AngsuranInfaq::with(['siswa.kelas', 'infaqGedung'])
                            ->where('id_siswa', $siswa->id)
                            ->orderBy('created_at', 'DESC')
                            ->paginate(10),
            'siswa' => $siswa
        ];
        
        return view('dashboard.siswa.infaq', $data);
    }
}