<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth',
            'privilege:admin'
        ]);
    }

    public function index()
    {
        $data = [
            'user' => User::find(auth()->user()->id),
            'kegiatan' => Kegiatan::orderBy('created_at', 'desc')->paginate(10),
        ];
        return view('dashboard.kegiatan.index', $data);
    }

    public function create()
    {
        return view('dashboard.kegiatan.create',
            ['user' => User::find(auth()->user()->id)]
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required',
            'biaya' => 'required|numeric',
            'tahun' => 'required|numeric',
        ]);

        Kegiatan::create($request->all());
        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'user' => User::find(auth()->user()->id),
            'kegiatan' => Kegiatan::findOrFail($id),
        ];
        return view('dashboard.kegiatan.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required',
            'biaya' => 'required|numeric',
            'tahun' => 'required|numeric',
        ]);

        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->update($request->all());

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return redirect()->route('kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}