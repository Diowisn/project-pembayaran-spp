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

    public function index()
    {
        $data = [
            'user' => User::find(auth()->user()->id),
            'kegiatan' => KegiatanTahunan::orderBy('id', 'DESC')->paginate(10),
        ];

        return view('dashboard.data-kegiatan-tahunan.index', $data);
    }

    public function create()
    {
        return view('dashboard.data-kegiatan-tahunan.create', [
            'user' => User::find(auth()->user()->id)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'nominal' => 'required|integer',
            // 'wajib' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ]);

        KegiatanTahunan::create($request->all());

        Alert::success('Berhasil!', 'Data kegiatan berhasil ditambahkan');
        return redirect()->route('data-kegiatan-tahunan.index');
    }

    public function edit($id)
    {
        return view('dashboard.data-kegiatan-tahunan.edit', [
            'user' => User::find(auth()->user()->id),
            'kegiatan' => KegiatanTahunan::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'nominal' => 'required|integer',
            // 'wajib' => 'required|boolean',
            'keterangan' => 'nullable|string',
        ]);

        $kegiatan = KegiatanTahunan::findOrFail($id);
        $kegiatan->update($request->all());

        Alert::success('Berhasil!', 'Data kegiatan berhasil diupdate');
        return redirect()->route('data-kegiatan-tahunan.index');
    }

    public function destroy($id) 
    {
        try {
            $kegiatan = KegiatanTahunan::withCount('siswaKegiatan')->findOrFail($id);

            if ($kegiatan->siswa_kegiatan_count > 0) {
                Alert::error('Gagal!', 'Tidak dapat menghapus karena ada siswa yang mengikuti kegiatan ini');
                return back();
            }

            if ($kegiatan->delete()) {
                Alert::success('Berhasil!', 'Data kegiatan berhasil dihapus');
            } else {
                Alert::error('Gagal!', 'Data kegiatan gagal dihapus');
            }

        } catch (\Exception $e) {
            Alert::error('Error!', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back();
    }
}