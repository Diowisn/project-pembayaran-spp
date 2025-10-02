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
use App\Models\Tabungan;
use App\Models\UangTahunan;
use App\Models\Kegiatan;
use App\Models\SiswaKegiatan;
use Illuminate\Support\Facades\Hash;

class SiswaLoginController extends Controller
{
    public function siswaLogin()
    {
        if (session('nisn') != null) {  
            return redirect('dashboard/siswa');
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
            if (Hash::check($request->password, $siswa->password)) {
                Session::put('id', $siswa->id);
                Session::put('nama', $siswa->nama);
                Session::put('nisn', $siswa->nisn);
                Session::put('id_kelas', $siswa->id_kelas);
                Session::put('nomor_telp', $siswa->nomor_telp);
                Session::put('alamat', $siswa->alamat);
                
                return redirect('dashboard/siswa');
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
        
        $siswa = Siswa::with('kelas')->find(Session::get('id'));
        
        $data = [
            'pembayaran' => Pembayaran::where('id_siswa', Session::get('id'))
                ->where('id_siswa', $siswa->id)
                ->orderBy('created_at', 'DESC')
                ->paginate(10),
            'siswa' => $siswa,
            'kelas' => Kelas::all(),
            'infaqGedung' => InfaqGedung::all(),
            'spp' => Spp::all()
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

    public function uangTahunan()
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $siswa = Siswa::find(Session::get('id'));
        
        $data = [
            'uangTahunanHistori' => UangTahunan::with(['siswa.kelas', 'petugas'])
                                ->where('id_siswa', $siswa->id)
                                ->orderBy('created_at', 'DESC')
                                ->paginate(10),
            'siswa' => $siswa,
            'saldo' => UangTahunan::where('id_siswa', $siswa->id)
                        ->latest()
                        ->first()
                        ->saldo ?? 0
        ];
        
        return view('dashboard.siswa.uang-tahunan', $data);
    }

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

    public function tabungan()
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $siswa = Siswa::find(Session::get('id'));
        
        $data = [
            'tabunganHistori' => Tabungan::with(['siswa.kelas', 'petugas'])
                                ->where('id_siswa', $siswa->id)
                                ->orderBy('created_at', 'DESC')
                                ->paginate(10),
            'siswa' => $siswa,
            'saldo' => Tabungan::where('id_siswa', $siswa->id)
                        ->latest()
                        ->first()
                        ->saldo ?? 0
        ];
        
        return view('dashboard.siswa.tabungan', $data);
    }

    public function kegiatan()
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $siswa = Siswa::find(Session::get('id'));
        
        $data = [
            'kegiatanHistori' => \App\Models\SiswaKegiatan::with(['siswa.kelas', 'kegiatan', 'petugas'])
                                ->where('id_siswa', $siswa->id)
                                ->orderBy('created_at', 'DESC')
                                ->paginate(10),
            'siswa' => $siswa,
            'totalDibayar' => \App\Models\SiswaKegiatan::where('id_siswa', $siswa->id)
                                ->sum('jumlah_bayar')
        ];
        
        return view('dashboard.siswa.kegiatan', $data);
    }

    public function dashboard()
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $siswa = Siswa::with(['kelas', 'spp', 'tabungan', 'infaqGedung', 'angsuranInfaq'])->find(Session::get('id'));
        
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
        
        $currentMonthName = $bulanIndo[now()->format('F')];
        $currentYear = now()->format('Y');
         
        $pembayaranTerakhir = Pembayaran::where('id_siswa', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
         
        $infaqTerakhir = AngsuranInfaq::where('id_siswa', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get(); 

        $kegiatanTerakhir = \App\Models\SiswaKegiatan::with('kegiatan')
            ->where('id_siswa', $siswa->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
         
        $statusPembayaran = Pembayaran::where('id_siswa', $siswa->id)
            ->where('bulan', strtolower($currentMonthName))
            ->where('tahun', $currentYear)
            ->first();
        
        $totalDibayarkan = Pembayaran::where('id_siswa', $siswa->id)
            ->whereYear('created_at', $currentYear)
            ->sum('jumlah_bayar');
        
        $tabungan = $siswa->tabungan()->orderBy('created_at', 'desc')->limit(3)->get();
        $saldoTabungan = $siswa->tabungan()->sum('debit') - $siswa->tabungan()->sum('kredit');
        $totalSetoran = $siswa->tabungan()->sum('debit');
        $totalPenarikan = $siswa->tabungan()->sum('kredit');
        
        $totalDibayarInfaq = $siswa->angsuranInfaq->sum('jumlah_bayar');
        $totalTagihanInfaq = $siswa->infaqGedung->nominal ?? 0;
        $sisaPembayaranInfaq = max($totalTagihanInfaq - $totalDibayarInfaq, 0);
        $persentaseInfaq = $totalTagihanInfaq > 0 ? ($totalDibayarInfaq / $totalTagihanInfaq) * 100 : 0;
        
        $totalDibayarKegiatan = \App\Models\SiswaKegiatan::where('id_siswa', $siswa->id)->sum('jumlah_bayar');
        $kegiatanLunas = \App\Models\SiswaKegiatan::where('id_siswa', $siswa->id)
                            ->where('is_lunas', true)
                            ->count();
        $totalKegiatan = \App\Models\SiswaKegiatan::where('id_siswa', $siswa->id)->count();
        
        $nominal_inklusi = $siswa->spp->nominal_inklusi ?? 0;
        
        return view('dashboard.siswa.dashboard', [
            'siswa' => $siswa,
            'pembayaranTerakhir' => $pembayaranTerakhir,
            'infaqTerakhir' => $infaqTerakhir,
            'kegiatanTerakhir' => $kegiatanTerakhir,
            'statusPembayaran' => $statusPembayaran,
            'currentMonthName' => $currentMonthName,
            'currentYear' => $currentYear,
            'totalDibayarkan' => $totalDibayarkan,
            'spp' => $siswa->spp,
            'tabungan' => $tabungan,
            'saldoTabungan' => $saldoTabungan,
            'totalSetoran' => $totalSetoran,
            'totalPenarikan' => $totalPenarikan,
            'totalDibayarInfaq' => $totalDibayarInfaq,
            'totalTagihanInfaq' => $totalTagihanInfaq,
            'sisaPembayaranInfaq' => $sisaPembayaranInfaq,
            'persentaseInfaq' => $persentaseInfaq,
            'totalDibayarKegiatan' => $totalDibayarKegiatan,
            'kegiatanLunas' => $kegiatanLunas,
            'totalKegiatan' => $totalKegiatan,
            'nominal_inklusi' => $nominal_inklusi
        ]);
    }
}