<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Spp;
use App\Models\InfaqGedung;
use Illuminate\Support\Facades\Log;
use Alert;

class SiswaController extends Controller
{
   
    public function __construct(){
         $this->middleware([
            'auth',
            'privilege:admin'
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
            'user' => User::find(auth()->user()->id),
            'siswa' => Siswa::orderBy('id', 'DESC')->paginate(10),
        ];
      
        return view('dashboard.data-siswa.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
public function create()
{
    $data = [
        'user' => User::find(auth()->user()->id),
        'kelas' => Kelas::all(),
        // 'spp' => Spp::all(),
        'infaq' => InfaqGedung::all(), 
    ];
  
    return view('dashboard.data-siswa.create', $data);
}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
public function store(Request $request)
{
    Log::debug('Data Request:', $request->all());

    $messages = [
        'required' => ':attribute tidak boleh kosong!',
        'numeric' => ':attribute harus berupa angka!',
        'integer' => ':attribute harus berupa bilangan bulat!',
        'unique' => ':attribute sudah terdaftar!'
    ];
    
    $validasi = $request->validate([
        'nisn' => 'required|numeric|unique:siswa,nisn|digits:12',
        'nis' => 'required|numeric|unique:siswa,nis|digits:8',
        'nama' => 'required|max:35',
        'id_kelas' => 'required|integer|exists:kelas,id', // Ubah dari 'kelas' ke 'id_kelas'
        'nomor_telp' => 'required|numeric', // Sesuaikan dengan name di form
        'alamat' => 'required',
        'id_infaq_gedung' => 'nullable|integer|exists:infaq_gedung,id',
    ], $messages);

    try {
        $siswa = Siswa::create([
            'nisn' => $request->nisn,
            'nis' => $request->nis,
            'nama' => $request->nama,
            'id_kelas' => $request->id_kelas, // Sesuaikan dengan name di form
            'nomor_telp' => $request->nomor_telp,
            'alamat' => $request->alamat,
            'id_infaq_gedung' => $request->id_infaq_gedung,
        ]);
        
        Alert::success('Berhasil!', 'Data Berhasil di Tambahkan');
        return redirect()->route('data-siswa.index'); // Gunakan named route
    } catch (\Exception $e) {
        Alert::error('Error!', 'Terjadi kesalahan: '.$e->getMessage());
        return back()->withInput()->withErrors($e->getMessage());
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
            'user' => User::find(auth()->user()->id),
            'siswa' => Siswa::find($id),
            'kelas' => Kelas::all(),
            // 'spp' => Spp::all(),
            'infaq' => InfaqGedung::all(),
        ];
      
        return view('dashboard.data-siswa.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
public function update(Request $request, $id)
{
    Log::debug('Data Request Update:', $request->all());
    
    $messages = [
        'required' => ':attribute tidak boleh kosong!',
        'numeric' => ':attribute harus berupa angka!',
        'integer' => ':attribute harus berupa bilangan bulat!',
        'max' => ':attribute maksimal :max karakter!'
    ];
    
    $validasi = $request->validate([
        'nisn' => 'required|numeric|digits:12|unique:siswa,nisn,'.$id,
        'nis' => 'required|numeric|digits:8|unique:siswa,nis,'.$id,
        'nama' => 'required|max:35',
        'id_kelas' => 'required|integer|exists:kelas,id',
        'nomor_telp' => 'required|numeric',
        'alamat' => 'required',
        'id_infaq_gedung' => 'nullable|integer|exists:infaq_gedung,id',
    ], $messages);

    try {
        $siswa = Siswa::findOrFail($id);
        $update = $siswa->update([
            'nisn' => $request->nisn,
            'nis' => $request->nis,
            'nama' => $request->nama,
            'id_kelas' => $request->id_kelas,
            'nomor_telp' => $request->nomor_telp,
            'alamat' => $request->alamat,
            'id_infaq_gedung' => $request->id_infaq_gedung,
        ]);
        
        if($update) {
            Alert::success('Berhasil!', 'Data Berhasil di Update');
            return redirect()->route('data-siswa.index');
        }
    } catch (\Exception $e) {
        Alert::error('Error!', 'Terjadi kesalahan: '.$e->getMessage());
        return back()->withInput()->withErrors($e->getMessage());
    }

    Alert::error('Error!', 'Gagal mengupdate data siswa');
    return back()->withInput();
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Siswa::find($id)->delete()) :
            Alert::success('Berhasil!', 'Data Berhasil di Hapus');
        else :
            Alert::error('Terjadi Kesalahan!', 'Data Gagal di Hapus');
        endif;
      
      return back();
    }
}
