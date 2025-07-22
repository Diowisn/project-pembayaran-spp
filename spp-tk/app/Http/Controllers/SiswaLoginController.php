<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alert;
use Session;
use App\Models\Siswa;
use App\Models\Pembayaran;
use App\Models\AngsuranInfaq;
use App\Models\Kelas;
use App\Models\InfaqGedung;
use App\Models\Spp;
use Illuminate\Support\Facades\Hash;

class SiswaLoginController extends Controller
{
    // Method untuk halaman login
    public function siswaLogin()
    {
        if (session('nisn') != null) {  
            return redirect('dashboard/siswa/histori');
        }
    
        return view('auth.siswa-login');
    }
    
    // Method proses login
    public function login(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string',
            'password' => 'required|string',
        ]);
        
        $siswa = Siswa::where('nisn', $request->nisn)->first();
        
        if ($siswa) {
            if (Hash::check($request->password, $siswa->password)) {
                Session::put('id', $siswa->id);
                Session::put('nama', $siswa->nama);
                Session::put('nisn', $siswa->nisn);
                Session::put('id_kelas', $siswa->id_kelas);
                Session::put('nomor_telp', $siswa->nomor_telp);
                Session::put('alamat', $siswa->alamat);
                
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
    
    // Method logout
    public function logout()
    {
        Session::flush();
        return redirect('login/siswa');
    }
    
    // Method halaman histori pembayaran
    public function index()
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $siswa = Siswa::with('kelas')->find(Session::get('id'));
        
        $data = [
            'pembayaran' => Pembayaran::where('id_siswa', Session::get('id'))->paginate(10),
            'siswa' => $siswa,
            'kelas' => Kelas::all(),
            'infaqGedung' => InfaqGedung::all(),
            'spp' => Spp::all()
        ];
        
        return view('dashboard.siswa.index', $data);
    }

    // Method halaman infaq
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

    // Method untuk mendapatkan data siswa (AJAX)
    public function getData()
    {
        if (!session('id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $siswa = Siswa::with('kelas')->find(session('id'));
        
        return response()->json([
            'siswa' => $siswa,
            'kelas' => Kelas::all()
        ]);
    }

    // Method untuk update profil
    public function updateProfile(Request $request)
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $id = Session::get('id');
        $siswa = Siswa::findOrFail($id);
        
        $messages = [
            'required' => ':attribute tidak boleh kosong!',
            'numeric' => ':attribute harus berupa angka!',
            'integer' => ':attribute harus berupa bilangan bulat!',
            'max' => ':attribute maksimal :max karakter!'
        ];
        
        $validasi = $request->validate([
            'nama' => 'required|max:35',
            'id_kelas' => 'required|integer|exists:kelas,id',
            'nomor_telp' => 'required|numeric',
            'alamat' => 'required',
            'password' => 'nullable|min:6|confirmed',
            'password_confirmation' => 'nullable|min:6'
        ], $messages);

        try {
            $data = [
                'nama' => $request->nama,
                'id_kelas' => $request->id_kelas,
                'nomor_telp' => $request->nomor_telp,
                'alamat' => $request->alamat,
            ];
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            
            $siswa->update($data);
            
            // Update session data
            Session::put('nama', $request->nama);
            Session::put('id_kelas', $request->id_kelas);
            Session::put('nomor_telp', $request->nomor_telp);
            Session::put('alamat', $request->alamat);
            
            Alert::success('Berhasil!', 'Data profil berhasil diperbarui');
            return redirect('dashboard/siswa/histori');
        } catch (\Exception $e) {
            Alert::error('Error!', 'Terjadi kesalahan: '.$e->getMessage());
            return back()->withInput();
        }
    }
}