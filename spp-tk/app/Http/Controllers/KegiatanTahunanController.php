<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanTahunan;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;

class KegiatanTahunanController extends Controller
{
    public function __construct(){
        $this->middleware([
            'auth',
            'privilege:admin'
        ]);
    }

    public function index(Request $request)
    {
        // Ambil parameter paket dari request
        $selectedPaket = $request->get('paket');
        
        $data = [
            'user' => User::find(auth()->user()->id),
            'paketList' => KegiatanTahunan::getPaketList(),
            'selectedPaket' => $selectedPaket,
        ];

        // Jika ada paket yang dipilih, tampilkan kegiatannya
        if ($selectedPaket) {
            $data['kegiatanPaket'] = KegiatanTahunan::getKegiatanByPaket($selectedPaket);
        }

        return view('dashboard.data-kegiatan-tahunan.index', $data);
    }

    public function store(Request $request)
    {
        // Validasi untuk menambah paket (hanya nama_paket)
        if (empty($request->nama_kegiatan)) {
            $request->validate([
                'nama_paket' => 'required|string|max:255',
            ]);
            
            // Cek apakah paket sudah ada
            $existingPaket = KegiatanTahunan::where('nama_paket', $request->nama_paket)
                ->whereNull('nama_kegiatan')
                ->first();
                
            if ($existingPaket) {
                Alert::error('Gagal!', 'Paket dengan nama ini sudah ada');
                return back();
            }
            
            // Buat record paket saja dengan nilai null untuk lainnya
            KegiatanTahunan::create([
                'nama_paket' => $request->nama_paket,
                'nama_kegiatan' => null,
                'nominal' => null,
                'keterangan' => null,
            ]);
            
            Alert::success('Berhasil!', 'Paket berhasil ditambahkan');
        } else {
            // Validasi untuk menambah kegiatan
            $request->validate([
                'nama_paket' => 'required|string|max:255',
                'nama_kegiatan' => 'required|string|max:255',
                'nominal' => 'required|integer',
                'keterangan' => 'nullable|string',
            ]);
            
            // Tambahkan kegiatan ke dalam paket
            KegiatanTahunan::create($request->all());
            Alert::success('Berhasil!', 'Kegiatan berhasil ditambahkan ke paket');
        }

        return redirect()->route('data-kegiatan-tahunan.index');
    }

    public function edit($id)
    {
        $kegiatan = KegiatanTahunan::findOrFail($id);
        
        return view('dashboard.data-kegiatan-tahunan.edit', [
            'user' => User::find(auth()->user()->id),
            'kegiatan' => $kegiatan,
            'paketList' => KegiatanTahunan::getPaketList(),
            'isPaket' => empty($kegiatan->nama_kegiatan),
        ]);
    }

    public function update(Request $request, $id)
    {
        $kegiatan = KegiatanTahunan::findOrFail($id);
        
        if (empty($kegiatan->nama_kegiatan)) {
            // Update paket
            $request->validate([
                'nama_paket' => 'required|string|max:255',
            ]);
            
            $kegiatan->update([
                'nama_paket' => $request->nama_paket,
            ]);
            
            Alert::success('Berhasil!', 'Paket berhasil diupdate');
        } else {
            // Update kegiatan
            $request->validate([
                'nama_paket' => 'required|string|max:255',
                'nama_kegiatan' => 'required|string|max:255',
                'nominal' => 'required|integer',
                'keterangan' => 'nullable|string',
            ]);
            
            $kegiatan->update($request->all());
            Alert::success('Berhasil!', 'Kegiatan berhasil diupdate');
        }

        return redirect()->route('data-kegiatan-tahunan.index');
    }

    public function destroy($id) 
    {
        try {
            $kegiatan = KegiatanTahunan::findOrFail($id);
            
            if (empty($kegiatan->nama_kegiatan)) {
                // Hapus paket - cek dulu apakah ada kegiatan dalam paket ini
                $kegiatanCount = KegiatanTahunan::where('nama_paket', $kegiatan->nama_paket)
                    ->whereNotNull('nama_kegiatan')
                    ->count();
                    
                if ($kegiatanCount > 0) {
                    Alert::error('Gagal!', 'Tidak dapat menghapus paket karena masih memiliki kegiatan');
                    return back();
                }
                
                if ($kegiatan->delete()) {
                    Alert::success('Berhasil!', 'Paket berhasil dihapus');
                }
            } else {
                // Hapus kegiatan individual - cek apakah ada siswa yang terkait
                if ($kegiatan->siswaKegiatan()->count() > 0) {
                    Alert::error('Gagal!', 'Tidak dapat menghapus karena ada siswa yang mengikuti kegiatan ini');
                    return back();
                }

                if ($kegiatan->delete()) {
                    Alert::success('Berhasil!', 'Kegiatan berhasil dihapus');
                }
            }

        } catch (\Exception $e) {
            Alert::error('Error!', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back();
    }
    
    // Method untuk menampilkan form tambah kegiatan ke paket
    public function createKegiatan($paket)
    {
        return view('dashboard.data-kegiatan-tahunan.create-kegiatan', [
            'user' => User::find(auth()->user()->id),
            'paket' => $paket,
        ]);
    }
}