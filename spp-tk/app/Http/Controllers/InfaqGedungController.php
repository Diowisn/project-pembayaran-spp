<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InfaqGedung;
use App\Models\User;
use RealRashid\SweetAlert\Facades\Alert;

class InfaqGedungController extends Controller
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
            'infaq' => InfaqGedung::orderBy('id', 'DESC')->paginate(10),
        ];

        return view('dashboard.infaq-gedung.index', $data);
    }

    public function create()
    {
        return view('dashboard.infaq-gedung.create', [
            'user' => User::find(auth()->user()->id)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'paket' => 'required|max:1',
            'nominal' => 'required|integer',
            'jumlah_angsuran' => 'required|integer',
            'nominal_per_angsuran' => 'required|integer',
        ]);

        InfaqGedung::create($request->all());

        Alert::success('Berhasil!', 'Data berhasil ditambahkan');
        return redirect()->route('infaq-gedung.index');
    }

    public function edit($id)
    {
        return view('dashboard.infaq-gedung.edit', [
            'user' => User::find(auth()->user()->id),
            'infaq' => InfaqGedung::findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'paket' => 'required|max:1',
            'nominal' => 'required|integer',
            'jumlah_angsuran' => 'required|integer',
            'nominal_per_angsuran' => 'required|integer',
        ]);

        $infaq = InfaqGedung::findOrFail($id);
        $infaq->update($request->all());

        Alert::success('Berhasil!', 'Data berhasil diupdate');
        return redirect()->route('infaq-gedung.index');
    }

    public function destroy($id) 
    {
        \Log::info("Attempting to delete infaq gedung with ID: {$id}");
        \Log::info("Request URL: " . request()->fullUrl());
        \Log::info("Request Method: " . request()->method());
        
        try {
            $infaq = InfaqGedung::withCount('siswa')->find($id);
            
            if (!$infaq) {
                \Log::error("Infaq gedung not found with ID: {$id}");
                Alert::error('Error!', 'Data tidak ditemukan');
                return back();
            }
            
            \Log::info("Found infaq gedung: " . json_encode($infaq->toArray()));
            
            if ($infaq->siswa_count > 0) {
                \Log::error("Cannot delete - has related siswa data");
                Alert::error('Gagal!', 'Tidak dapat menghapus karena data terkait dengan siswa');
                return back();
            }
            
            $infaq->delete();
            
            \Log::info("Successfully deleted infaq gedung with ID: {$id}");
            
            Alert::success('Berhasil!', 'Data berhasil dihapus');
            return redirect()->route('infaq-gedung.index');
        } catch (\Exception $e) {
            \Log::error("Delete error: " . $e->getMessage());
            Alert::error('Error!', 'Gagal menghapus data: ' . $e->getMessage());
            return back();
        }
    }
}
