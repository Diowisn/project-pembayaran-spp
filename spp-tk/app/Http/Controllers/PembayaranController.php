<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\Siswa;
use Alert;

class PembayaranController extends Controller
{
   
   public function __construct(){
         $this->middleware([
            'auth',
            'privilege:admin&petugas'
         ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'pembayaran' => Pembayaran::with(['petugas'])->orderBy('id', 'DESC')->paginate(10),
            'user' => User::find(auth()->user()->id)
        ];
      
        return view('dashboard.entri-pembayaran.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

// public function getSiswaByNisn($nisn)
// {
//     $siswa = Siswa::with('spp')->where('nisn', $nisn)->first();

//     if (!$siswa) {
//         return response()->json(['message' => 'Siswa tidak ditemukan'], 404);
//     }

//     return response()->json([
//         'data' => [
//             'id_siswa' => $siswa->id,
//             'nama' => $siswa->nama,
//             'nominal_spp' => $siswa->spp->nominal_spp ?? 0,
//             'nominal_konsumsi' => $siswa->spp->nominal_konsumsi ?? 0,
//             'nominal_fullday' => $siswa->spp->nominal_fullday ?? 0,
//         ]
//     ]);
// }

public function cariSiswa(Request $request)
{
    $request->validate([
        'nisn' => 'required|exists:siswa,nisn'
    ]);

    $siswa = Siswa::with('spp')->where('nisn', $request->nisn)->first();

    return view('dashboard.entri-pembayaran.index', [
        'pembayaran' => Pembayaran::with(['petugas'])->orderBy('id', 'DESC')->paginate(10),
        'siswa' => $siswa,
        'user' => User::find(auth()->user()->id)
    ]);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'id_siswa' => 'required|exists:siswa,id',
        'nisn' => 'required|exists:siswa,nisn',
        'nominal_pembayaran' => 'required|numeric|min:'.$request->jumlah_tagihan,
        'jumlah_tagihan' => 'required|numeric'
    ], [
        'min' => 'Pembayaran tidak boleh kurang dari :min',
        'required' => 'Field :attribute wajib diisi'
    ]);

    if ($validator->fails()) {
        return redirect()
                ->route('pembayaran.cari-siswa', ['nisn' => $request->nisn])
                ->withErrors($validator)
                ->withInput();
    }

    try {
        $siswa = Siswa::with('spp')->findOrFail($request->id_siswa);
        
        $pembayaran = Pembayaran::create([
            'id_petugas' => auth()->id(),
            'id_siswa' => $request->id_siswa,
            'id_spp' => $siswa->id_spp,
            'bulan' => date('m'),
            'tahun' => date('Y'),
            'nominal_spp' => $siswa->spp->nominal_spp,
            'nominal_konsumsi' => $siswa->spp->nominal_konsumsi ?? 0,
            'nominal_fullday' => $siswa->spp->nominal_fullday ?? 0,
            'jumlah_bayar' => $request->nominal_pembayaran,
            'kembalian' => $request->nominal_pembayaran - $request->jumlah_tagihan,
            'tgl_bayar' => now(),
            'is_lunas' => true,
        ]);

        Alert::success('Berhasil!', 'Pembayaran berhasil disimpan!');
        return redirect()->route('pembayaran.cari-siswa', ['nisn' => $siswa->nisn]);

    } catch (\Exception $e) {
        Log::error('Error creating pembayaran: '.$e->getMessage());
        Alert::error('Error!', 'Terjadi kesalahan saat menyimpan pembayaran');
        return redirect()->route('pembayaran.cari-siswa', ['nisn' => $request->nisn])
               ->withInput();
    }
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
            'edit' => Pembayaran::find($id),
            'user' => User::find(auth()->user()->id)
         ];
         
         return view('dashboard.entri-pembayaran.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $id)
    {
         $message = [
            'required' => ':attribute harus di isi',
            'numeric' => ':attribute harus berupa angka',
            'min' => ':attribute minimal harus :min angka',
            'max' => ':attribute maksimal harus :max angka',
         ];
         
        $req->validate([
            'nisn' => 'required',
            'spp_bulan' => 'required',
            'jumlah_bayar' => 'required|numeric'
         ], $message);
         
         $pembayaran = Pembayaran::find($id);
         
         $pembayaran->update([
             'spp_bulan' => $req->spp_bulan,
            'jumlah_bayar' => $req->jumlah_bayar
         ]);
         
         if(Siswa::where('nisn',$req->nisn)->exists() == false):
            Alert::error('Terjadi Kesalahan!', 'Siswa dengan NISN ini Tidak di Temukan');
           return back();
            exit;
         endif;

         if($req->nisn != $pembayaran->siswa->nisn) :
            $siswa = Siswa::where('nisn',$req->nisn)->get();
         
            foreach($siswa as $val){
               $id_siswa = $val->id;
            }
            
            $pembayaran->update([
               'id_siswa' => $id_siswa,
            ]);
         endif;
         
         Alert::success('Berhasil!', 'Pembayaran berhasil di Edit');
         return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Pembayaran::find($id)->delete()) :
            Alert::success('Berhasil!', 'Pembayaran Berhasil di Hapus!');
         else :
            Alert::success('Terjadi Kesalahan!', 'Pembayaran Gagal di Tambahkan!');
         endif;
         
         return back();
    }
}
