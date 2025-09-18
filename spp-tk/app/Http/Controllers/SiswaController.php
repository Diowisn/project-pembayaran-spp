<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Spp;
use App\Models\InfaqGedung;
use App\Models\Inklusi;
use App\Models\KegiatanTahunan;
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
    public function index(Request $request)
    {
        $query = Siswa::with(['kelas', 'spp', 'infaqGedung']);
        
        // Menentukan jumlah item per halaman (default 10)
        $perPage = $request->get('per_page', 10);
        
        if ($request->filled('kelas_id')) {
            $query->where('id_kelas', $request->kelas_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('sort_by') && $request->filled('order')) {
            $query->orderBy($request->sort_by, $request->order);
        } else {
            $query->orderBy('id', 'desc');
        }

        return view('dashboard.data-siswa.index', [
            'user' => User::find(auth()->user()->id),
            'siswa' => $query->paginate($perPage)->appends($request->all()),
            'allKelas' => Kelas::all(),
        ]);
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
            'spp' => Spp::with('kelas')->get(),
            'infaq' => InfaqGedung::all(), 
            'inklusi' => Inklusi::all(),
            'paketKegiatan' => KegiatanTahunan::whereNull('nama_kegiatan')->get(),
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
            'nisn' => 'required|numeric|unique:siswa,nisn|digits:8',
            'nama' => 'required|max:35',
            'id_kelas' => 'required|integer|exists:kelas,id',
            'inklusi' => 'nullable|boolean',
            'id_inklusi' => 'nullable|integer|exists:inklusi,id',
            'nomor_telp' => 'required|numeric',
            'alamat' => 'required',
            'id_infaq_gedung' => 'nullable|integer|exists:infaq_gedung,id',
            'id_spp' => 'required|integer|exists:spp,id',
            'id_paket_kegiatan' => 'nullable|integer|exists:kegiatan_tahunan,id',
        ], $messages);

        try {
            $siswa = Siswa::create([
                'nisn' => $request->nisn,
                'nama' => $request->nama,
                'id_kelas' => $request->id_kelas, 
                'inklusi' => $request->has('inklusi'),
                'id_inklusi' => $request->id_inklusi,
                'nomor_telp' => $request->nomor_telp,
                'alamat' => $request->alamat,
                'id_infaq_gedung' => $request->id_infaq_gedung,
                'id_spp' => $request->id_spp,
                'id_paket_kegiatan' => $request->id_paket_kegiatan,
            ]);
            
            Alert::success('Berhasil!', 'Data Berhasil di Tambahkan');
            return redirect()->route('data-siswa.index');
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
            'spp' => Spp::all(),
            'infaq' => InfaqGedung::all(),
            'inklusi' => Inklusi::all(),
            'paketKegiatan' => KegiatanTahunan::whereNull('nama_kegiatan')->get(),
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
            'nisn' => 'required|numeric|digits:8|unique:siswa,nisn,'.$id,
            'nama' => 'required|max:50',
            'id_kelas' => 'required|integer|exists:kelas,id',
            'inklusi' => 'nullable|boolean',
            'id_inklusi' => 'nullable|integer|exists:inklusi,id',
            'nomor_telp' => 'required|numeric',
            'alamat' => 'required',
            'id_infaq_gedung' => 'nullable|integer|exists:infaq_gedung,id',
            'id_spp' => 'required|integer|exists:spp,id',
            'id_paket_kegiatan' => 'nullable|integer|exists:kegiatan_tahunan,id'
        ], $messages);

        try {
            $siswa = Siswa::findOrFail($id);
            $update = $siswa->update([
                'nisn' => $request->nisn,
                'nama' => $request->nama,
                'id_kelas' => $request->id_kelas,
                'inklusi' => $request->has('inklusi'),
                'id_inklusi' => $request->id_inklusi,
                'nomor_telp' => $request->nomor_telp,
                'alamat' => $request->alamat,
                'id_infaq_gedung' => $request->id_infaq_gedung,
                'id_spp' => $request->id_spp,
                'id_paket_kegiatan' => $request->id_paket_kegiatan,
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
