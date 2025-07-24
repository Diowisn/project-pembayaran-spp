<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tabungan;
use App\Models\Siswa;
use App\Models\Pembayaran;
use Alert;
use PDF;

class TabunganController extends Controller
{
    public function __construct()
    {
        $this->middleware([
            'auth',
            'privilege:admin&petugas'
        ]);
    }

    /**
     * Display a listing of all savings transactions
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        // Debug - tampilkan parameter search
        Log::debug('Search parameter:', ['search' => $search]);
        
        $tabungan = Tabungan::with(['siswa', 'petugas', 'pembayaran'])
            ->when($search, function($query) use ($search) {
                return $query->whereHas('siswa', function($q) use ($search) {
                    $q->where('nisn', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Debug - tampilkan data yang diambil
        \Log::debug('Tabungan data:', ['count' => $tabungan->count()]);
        
        return view('dashboard.tabungan.index', [
            'tabungan' => $tabungan,
            'search' => $search,
            'user' => auth()->user()
        ]);
    }

    /**
     * Show detail savings for a specific student
     */
    public function show($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        $tabungan = Tabungan::where('id_siswa', $id)
            ->with(['petugas', 'pembayaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $saldo = Tabungan::where('id_siswa', $id)
            ->latest()
            ->first()
            ->saldo ?? 0;

        return view('dashboard.tabungan.show', [
            'siswa' => $siswa,
            'tabungan' => $tabungan,
            'saldo' => $saldo,
            'user' => auth()->user()
        ]);
    }

    /**
     * Process withdrawal from savings
     */
    public function tarik(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($id);
            
            $saldo_terakhir = Tabungan::where('id_siswa', $id)
                ->latest()
                ->first();
            
            $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
            
            if ($request->jumlah > $saldo_sebelumnya) {
                return back()->with('error', 'Saldo tidak mencukupi');
            }

            $saldo_sekarang = $saldo_sebelumnya - $request->jumlah;

            Tabungan::create([
                'id_siswa' => $id,
                'id_petugas' => auth()->id(),
                'debit' => 0,
                'kredit' => $request->jumlah,
                'saldo' => $saldo_sekarang,
                'keterangan' => $request->keterangan,
            ]);

            DB::commit();

            Alert::success('Berhasil!', 'Penarikan tabungan berhasil');
            return redirect()->route('tabungan.show', $id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error withdrawing savings: '.$e->getMessage());
            Alert::error('Gagal!', 'Terjadi kesalahan saat melakukan penarikan');
            return back()->withInput();
        }
    }

    /**
     * Generate savings report PDF
     */
    public function generateReport($id)
    {
        $siswa = Siswa::findOrFail($id);
        
        $tabungan = Tabungan::where('id_siswa', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $saldo = $tabungan->last()->saldo ?? 0;

        $logoPath = public_path('img/amanah31.png');
        $logoData = base64_encode(file_get_contents($logoPath));

        $pdf = PDF::loadView('pdf.laporan-tabungan', [
            'siswa' => $siswa,
            'tabungan' => $tabungan,
            'saldo' => $saldo,
            'logoData' => $logoData,
            'tanggal' => now()->format('d F Y')
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Tabungan-'.$siswa->nisn.'-'.$siswa->nama.'.pdf');
    }
}