<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\AngsuranInfaq;
use App\Models\Siswa;
use App\Models\User;
use App\Models\InfaqGedung;
use Alert;
use PDF;
use Log;

class InfaqController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth',
            'privilege:admin&petugas'
        ]);
    }

    public function index(Request $request)
    {
        $query = AngsuranInfaq::with(['siswa', 'infaqGedung']);

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
            $validColumns = ['created_at', 'jumlah_bayar', 'angsuran_ke'];
            $sortBy = in_array($request->sort_by, $validColumns) ? $request->sort_by : 'created_at';
            
            $query->orderBy($sortBy, $request->order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return view('dashboard.infaq.index', [
            'angsuran' => $query->paginate(10)->appends($request->all()),
            'user' => User::find(auth()->user()->id),
            'search' => $request->search ?? '',
            'sort_by' => $request->sort_by ?? 'created_at',
            'order' => $request->order ?? 'desc',
        ]);
    }

    public function cariSiswa(Request $request)
    {
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn'
        ]);

        $siswa = Siswa::with(['infaqGedung', 'angsuranInfaq'])->where('nisn', $request->nisn)->first();

        // Hitung total yang sudah dibayar
        $totalDibayar = $siswa->angsuranInfaq->sum('jumlah_bayar');
        $sisaPembayaran = $siswa->infaqGedung ? ($siswa->infaqGedung->nominal - $totalDibayar) : 0;

        return view('dashboard.infaq.index', [
            'angsuran' => AngsuranInfaq::with(['siswa'])->orderBy('id', 'DESC')->paginate(10),
            'siswa' => $siswa,
            'total_dibayar' => $totalDibayar,
            'sisa_pembayaran' => $sisaPembayaran,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswa,id',
            'nisn' => 'required|exists:siswa,nisn',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tgl_bayar' => 'required|date'
        ], [
            'required' => 'Field :attribute wajib diisi',
            'min' => 'Pembayaran tidak boleh kurang dari :min'
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->route('infaq.cari-siswa', ['nisn' => $request->nisn])
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            $siswa = Siswa::with(['infaqGedung', 'angsuranInfaq'])->findOrFail($request->id_siswa);
            
            // Hitung angsuran ke berapa
            $angsuranKe = $siswa->angsuranInfaq->count() + 1;
            
            // Buat pembayaran infaq
            $angsuran = AngsuranInfaq::create([
                'id_siswa' => $request->id_siswa,
                'angsuran_ke' => $angsuranKe,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tgl_bayar' => $request->tgl_bayar
            ]);

            Alert::success('Berhasil!', 'Pembayaran infaq berhasil disimpan!');
            return redirect()->route('infaq.cari-siswa', ['nisn' => $siswa->nisn]);

        } catch (\Exception $e) {
            Log::error('Error creating pembayaran infaq: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat menyimpan pembayaran infaq');
            return redirect()->route('infaq.cari-siswa', ['nisn' => $request->nisn])
                   ->withInput();
        }
    }

public function edit($id)
{
    $angsuran = AngsuranInfaq::with(['siswa', 'infaqGedung'])->findOrFail($id);
    
    return view('dashboard.infaq.edit', [
        'edit' => $angsuran,
        'user' => User::find(auth()->user()->id)
    ]);
}

public function update(Request $request, $id)
{
    $angsuran = AngsuranInfaq::with(['siswa', 'infaqGedung'])->findOrFail($id);
    
    $validator = Validator::make($request->all(), [
        'angsuran_ke' => 'required|numeric|min:1',
        'jumlah_bayar' => 'required|numeric|min:1',
        'tgl_bayar' => 'required|date'
    ], [
        'required' => 'Field :attribute wajib diisi',
        'min' => ':attribute tidak boleh kurang dari :min'
    ]);

    if ($validator->fails()) {
        return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
    }

    try {
        $angsuran->update([
            'angsuran_ke' => $request->angsuran_ke,
            'jumlah_bayar' => $request->jumlah_bayar,
            'tgl_bayar' => $request->tgl_bayar
        ]);

        Alert::success('Berhasil!', 'Pembayaran infaq berhasil diperbarui');
        return redirect()->route('infaq.index');

    } catch (\Exception $e) {
        Log::error('Error updating pembayaran infaq: '.$e->getMessage());
        Alert::error('Error!', 'Terjadi kesalahan saat memperbarui pembayaran infaq');
        return back()->withInput();
    }
}

    public function destroy($id)
    {
        if(AngsuranInfaq::find($id)->delete()) {
            Alert::success('Berhasil!', 'Pembayaran infaq berhasil dihapus!');
        } else {
            Alert::error('Terjadi Kesalahan!', 'Pembayaran infaq gagal dihapus!');
        }
        
        return back();
    }

    public function generate($id)
    {
        $angsuran = AngsuranInfaq::with(['siswa', 'infaqGedung'])->findOrFail($id);
        
        $pdf = PDF::loadView('pdf.bukti-infaq', compact('angsuran'))
                  ->setPaper('a5', 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                      'dpi' => 150
                  ]);
        
        return $pdf->download('Bukti-Pembayaran-Infaq-' . $angsuran->siswa->nama . '.pdf');
    }
}