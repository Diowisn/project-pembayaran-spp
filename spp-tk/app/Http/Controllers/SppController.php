<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spp;
use App\Models\User;
use App\Models\Kelas;
use App\Models\InfaqGedung;
use App\Models\Pembayaran;
use Alert;

class SppController extends Controller
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
            'spp' => Spp::with('kelas')->orderBy('id', 'ASC')->paginate(10),
            'user' => User::find(auth()->user()->id),
            'kelas' => Kelas::all(),
            'infaqGedung' => InfaqGedung::all()
        ];
      
        return view('dashboard.data-spp.index', $data);
    }

    public function create()
    {
        $data = [
            'kelas' => Kelas::all(),
            // 'infaqGedung' => InfaqGedung::all(),
            'user' => User::find(auth()->user()->id)
        ];
        
        return view('dashboard.data-spp.create', $data);
    }

    public function store(Request $request)
    {
        $messages = [
            'required' => ':attribute tidak boleh kosong!',
            'numeric' => ':attribute harus berupa angka!',
            'min' => ':attribute minimal harus :min angka!',
            'max' => ':attribute maksimal harus :max angka!',
            'integer' => ':attribute harus berupa nilai uang tanpa titik!'
        ];
        
        $validasi = $request->validate([
            'tahun' => 'required|min:4|max:4',
            'id_kelas' => 'required|exists:kelas,id',
            'nominal_spp' => 'required|integer',
            'nominal_konsumsi' => 'nullable|integer',
            'nominal_fullday' => 'nullable|integer',
            // 'id_infaq_gedung' => 'nullable|exists:infaq_gedung,id'
        ], $messages);
        
        // Bersihkan nominal dari format Rupiah
        $nominal_spp = str_replace('.', '', $request->nominal_spp);
        $nominal_konsumsi = $request->nominal_konsumsi ? str_replace('.', '', $request->nominal_konsumsi) : null;
        $nominal_fullday = $request->nominal_fullday ? str_replace('.', '', $request->nominal_fullday) : null;
        
        if($validasi) :
            $store = Spp::create([
                'tahun' => $request->tahun,
                'id_kelas' => $request->id_kelas,
                'nominal_spp' => $nominal_spp,
                'nominal_konsumsi' => $nominal_konsumsi,
                'nominal_fullday' => $nominal_fullday,
                // 'id_infaq_gedung' => $request->id_infaq_gedung
            ]);
            
            if($store) :
                Alert::success('Berhasil!', 'Data Berhasil Ditambahkan');
            else :
                Alert::error('Gagal!', 'Data Gagal Ditambahkan');
            endif;
        endif;
        
        return redirect('dashboard/data-spp');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = [
            'edit' => Spp::with(['kelas', 'infaqGedung'])->find($id),
            'kelas' => Kelas::all(),
            'infaqGedung' => InfaqGedung::all(),
            'user' => User::find(auth()->user()->id)
        ];
    
        return view('dashboard.data-spp.edit', $data);
    }

    public function update(Request $req, $id)
    {
        $messages = [
            'required' => ':attribute tidak boleh kosong!',
            'numeric' => ':attribute harus berupa angka!',
            'min' => ':attribute minimal harus :min angka!',
            'max' => ':attribute maksimal harus :max angka!',
            'integer' => ':attribute harus berupa nilai uang tanpa titik!'
        ];
        
        $validasi = $req->validate([
            'tahun' => 'required|min:4|max:4',
            'id_kelas' => 'required|exists:kelas,id',
            'nominal_spp' => 'required|integer',
            'nominal_konsumsi' => 'nullable|integer',
            'nominal_fullday' => 'nullable|integer',
            // 'id_infaq_gedung' => 'nullable|exists:infaq_gedung,id'
        ], $messages);
        
        // Bersihkan nominal dari format Rupiah
        $nominal_spp = str_replace('.', '', $req->nominal_spp);
        $nominal_konsumsi = $req->nominal_konsumsi ? str_replace('.', '', $req->nominal_konsumsi) : null;
        $nominal_fullday = $req->nominal_fullday ? str_replace('.', '', $req->nominal_fullday) : null;
        
        if($update = Spp::find($id)) :         
            $stat = $update->update([
                'tahun' => $req->tahun,
                'id_kelas' => $req->id_kelas,
                'nominal_spp' => $nominal_spp,
                'nominal_konsumsi' => $nominal_konsumsi,
                'nominal_fullday' => $nominal_fullday,
                // 'id_infaq_gedung' => $req->id_infaq_gedung
            ]);
            
            if($stat) :
                Alert::success('Berhasil!', 'Data Berhasil di Edit');
            else :
                Alert::error('Terjadi Kesalahan!', 'Data Gagal di Edit');
                return back();
            endif;
        endif;
        
        return redirect('dashboard/data-spp');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $spp = Spp::findOrFail($id);
            
            if ($spp->pembayaran()->exists()) {
                Alert::error('Gagal!', 'Tidak dapat menghapus SPP karena sudah ada data pembayaran terkait');
                return back();
            }
            
            if ($spp->delete()) {
                Alert::success('Berhasil!', 'Data SPP berhasil dihapus');
            } else {
                Alert::error('Gagal!', 'Data SPP gagal dihapus');
            }
        } catch (\Exception $e) {
            Alert::error('Error!', 'Terjadi kesalahan: ' . $e->getMessage());
        }
        
        return back();
    }
}
