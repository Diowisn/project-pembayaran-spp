<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inklusi;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;

class InklusiController extends Controller
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
            'inklusi' => Inklusi::orderBy('id', 'DESC')->paginate(10),
        ];

        return view('dashboard.data-inklusi.index', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|max:50',
            'nominal' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        Inklusi::create($request->all());

        Alert::success('Berhasil!', 'Data berhasil ditambahkan');
        return redirect()->route('data-inklusi.index');
    }

    public function edit($id)
    {
        return view('dashboard.data-inklusi.edit', [
            'user' => User::find(auth()->user()->id),
            'inklusi' => Inklusi::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_paket' => 'required|max:50',
            'nominal' => 'required|integer',
            'keterangan' => 'nullable|string',
        ]);

        $inklusi = Inklusi::findOrFail($id);
        $inklusi->update($request->all());

        Alert::success('Berhasil!', 'Data berhasil diupdate');
        return redirect()->route('data-inklusi.index');
    }

    public function destroy($id) 
    {
        try {
            $inklusi = Inklusi::withCount('siswa')->findOrFail($id);

            if ($inklusi->siswa_count > 0) {
                Alert::error('Gagal!', 'Tidak dapat menghapus karena data terkait dengan siswa');
                return back();
            }

            $inklusi->delete();
            Alert::success('Berhasil!', 'Data berhasil dihapus');

        } catch (\Exception $e) {
            Alert::error('Error!', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return back();
    }
}