<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\Siswa;
use Alert;
use PDF;

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
public function index(Request $request)
{
    $query = Pembayaran::with(['petugas', 'siswa', 'siswa.spp']);

    // Fitur Pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('siswa', function($q) use ($search) {
            $q->where('nama', 'like', '%' . $search . '%')
              ->orWhere('nisn', 'like', '%' . $search . '%');
        });
    }

    // Fitur Sorting
    if ($request->filled('sort_by') && $request->filled('order')) {
        // Validasi kolom sorting untuk keamanan
        $validColumns = ['created_at', 'jumlah_bayar'];
        $sortBy = in_array($request->sort_by, $validColumns) ? $request->sort_by : 'created_at';
        
        $query->orderBy($sortBy, $request->order);
    } else {
        $query->orderBy('created_at', 'desc');
    }

    return view('dashboard.entri-pembayaran.index', [
        'pembayaran' => $query->paginate(10)->appends($request->all()),
        'user' => User::find(auth()->user()->id),
        'search' => $request->search ?? '',
        'sort_by' => $request->sort_by ?? 'created_at',
        'order' => $request->order ?? 'desc',
    ]);
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
        'bulan' => 'required|string',
        'nominal_pembayaran' => 'required|numeric|min:'.$request->jumlah_tagihan,
        'jumlah_tagihan' => 'required|numeric'
    ], [
        'bulan.required' => 'Bulan pembayaran harus dipilih',
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
        
        // Tambahkan tahun dari input bulan
        $bulan = strtolower($request->bulan);
        $tahun = $request->tahun ?? date('Y'); // Pastikan tahun disimpan
        
        $pembayaran = Pembayaran::create([
            'id_petugas' => auth()->id(),
            'id_siswa' => $request->id_siswa,
            'id_spp' => $siswa->id_spp,
            'bulan' => $bulan,
            'tahun' => $tahun, // Simpan tahun
            'nominal_spp' => $siswa->spp->nominal_spp,
            'nominal_konsumsi' => $siswa->spp->nominal_konsumsi ?? 0,
            'nominal_fullday' => $siswa->spp->nominal_fullday ?? 0,
            'jumlah_bayar' => $request->nominal_pembayaran,
            'kembalian' => $request->nominal_pembayaran - $request->jumlah_tagihan,
            'tgl_bayar' => now(),
            'is_lunas' => true,
        ]);

        // Redirect ke halaman yang menampilkan data baru
        Alert::success('Berhasil!', 'Pembayaran berhasil disimpan!');
        return redirect()->route('entry-pembayaran.index'); // Ganti redirect ke halaman list

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
    $pembayaran = Pembayaran::with('siswa.spp')->findOrFail($id);
    
    $data = [
        'edit' => $pembayaran,
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
public function update(Request $request, $id)
{
    $pembayaran = Pembayaran::with('siswa.spp')->findOrFail($id);
    
    $messages = [
        'required' => ':attribute harus diisi',
        'numeric' => ':attribute harus berupa angka',
        'min' => 'Pembayaran tidak boleh kurang dari tagihan'
    ];

    $validator = Validator::make($request->all(), [
        'bulan' => 'required',
        'jumlah_bayar' => 'required|numeric|min:'.($pembayaran->nominal_spp + 
                          ($pembayaran->nominal_konsumsi ?? 0) + 
                          ($pembayaran->nominal_fullday ?? 0)),
        'tgl_bayar' => 'required|date',
        'is_lunas' => 'required|boolean'
    ], $messages);

    if ($validator->fails()) {
        return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
    }

    try {
        $pembayaran->update([
            'bulan' => $request->bulan,
            'jumlah_bayar' => $request->jumlah_bayar,
            'tgl_bayar' => $request->tgl_bayar,
            'is_lunas' => $request->is_lunas,
            'kembalian' => $request->jumlah_bayar - ($pembayaran->nominal_spp + 
                          ($pembayaran->nominal_konsumsi ?? 0) + 
                          ($pembayaran->nominal_fullday ?? 0))
        ]);

        Alert::success('Berhasil!', 'Pembayaran berhasil diperbarui');
        return redirect()->route('entry-pembayaran.index');

    } catch (\Exception $e) {
        Log::error('Error updating pembayaran: '.$e->getMessage());
        Alert::error('Error!', 'Terjadi kesalahan saat memperbarui pembayaran');
        return back()->withInput();
    }
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

    public function generate($id)
    {
        ini_set('max_execution_time', 300);
        
        $pembayaran = Pembayaran::with(['siswa', 'siswa.kelas', 'petugas'])->findOrFail($id);
        
        $pdf = PDF::loadView('pdf.bukti', compact('pembayaran'))
                  ->setPaper('a5', 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                      'dpi' => 150
                  ]);
        
        return $pdf->download('Bukti-Pembayaran-SPP-' . $pembayaran->siswa->nama . '.pdf');
    }
}
