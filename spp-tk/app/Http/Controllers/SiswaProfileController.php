<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alert;
use Session;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\InfaqGedung;
use App\Models\Spp;
use Illuminate\Support\Facades\Hash;

class SiswaProfileController extends Controller
{
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

    public function show()
    {
        if (session('nisn') == null) {  
            return redirect('login/siswa');
        }
        
        $siswa = Siswa::with('kelas')->findOrFail(Session::get('id'));
        
        $data = [
            'siswa' => $siswa,
            'kelas' => Kelas::all(), 
            'kelasSiswa' => $siswa->kelas, 
            'infaqGedung' => InfaqGedung::all(),
            'spp' => Spp::all()
        ];
        
        return view('dashboard.siswa.index', $data);
    }

    public function update(Request $request)
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
            
            Alert::success('Berhasil!', 'Data profil berhasil diperbarui');
            return redirect()->route('siswa.index');
        } catch (\Exception $e) {
            Alert::error('Error!', 'Terjadi kesalahan: '.$e->getMessage());
            return back()->withInput();
        }
    }
}