<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\SiswaKegiatan;
use App\Models\Siswa;
use App\Models\KegiatanTahunan;
use App\Models\User;
use App\Models\Tabungan;
use Alert;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class KegiatanSiswaController extends Controller
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
        $query = SiswaKegiatan::with(['siswa', 'kegiatan'])
            ->where('partisipasi', 'ikut');

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
            $validColumns = ['created_at', 'jumlah_bayar', 'angsuran_ke', 'kembalian', 'tgl_bayar'];
            $sortBy = in_array($request->sort_by, $validColumns) ? $request->sort_by : 'created_at';
            
            $query->orderBy($sortBy, $request->order);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return view('dashboard.entri-kegiatan.index', [
            'pembayaran' => $query->paginate(10)->appends($request->all()),
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

        $siswa = Siswa::with(['kegiatanSiswa.kegiatan'])->where('nisn', $request->nisn)->first();
        $semuaKegiatan = KegiatanTahunan::all();

        $detailKegiatan = [];
        $totalTagihanKegiatan = 0;
        $totalDibayarSemua = 0;
        $sisaSemua = 0;

        foreach ($semuaKegiatan as $kegiatan) {
            $partisipasi = $siswa->kegiatanSiswa
                ->where('id_kegiatan', $kegiatan->id)
                ->first();
            
            $statusPartisipasi = $partisipasi ? $partisipasi->partisipasi : 'ikut';
            $alasanTidakIkut = $partisipasi ? $partisipasi->alasan_tidak_ikut : null;

            if ($statusPartisipasi === 'tidak_ikut') {
                $detailKegiatan[] = [
                    'kegiatan' => $kegiatan,
                    'partisipasi' => 'tidak_ikut',
                    'alasan_tidak_ikut' => $alasanTidakIkut,
                    'total_dibayar' => 0,
                    'sisa_pembayaran' => 0,
                    'is_lunas' => true
                ];
                continue;
            }

            $totalDibayar = $siswa->kegiatanSiswa
                ->where('id_kegiatan', $kegiatan->id)
                ->where('partisipasi', 'ikut') 
                ->sum('jumlah_bayar');
            
            $sisaPembayaran = max($kegiatan->nominal - $totalDibayar, 0);
            $isLunas = ($totalDibayar >= $kegiatan->nominal);

            $detailKegiatan[] = [
                'kegiatan' => $kegiatan,
                'partisipasi' => 'ikut',
                'alasan_tidak_ikut' => null,
                'total_dibayar' => $totalDibayar,
                'sisa_pembayaran' => $sisaPembayaran,
                'is_lunas' => $isLunas
            ];

            $totalTagihanKegiatan += $kegiatan->nominal;
            $totalDibayarSemua += $totalDibayar;
        }

        $sisaSemua = max($totalTagihanKegiatan - $totalDibayarSemua, 0);

        $pembayaranQuery = SiswaKegiatan::with(['siswa', 'kegiatan'])
            ->where('id_siswa', $siswa->id)
            ->where('partisipasi', 'ikut')
            ->orderBy('id', 'DESC');

        $pembayaran = $pembayaranQuery->paginate(10)->appends(['nisn' => $request->nisn]);

        return view('dashboard.entri-kegiatan.index', [
            'pembayaran' => $pembayaran,
            'siswa' => $siswa,
            'semuaKegiatan' => $semuaKegiatan,
            'detailKegiatan' => $detailKegiatan,
            'total_tagihan_kegiatan' => $totalTagihanKegiatan,
            'total_dibayar_semua' => $totalDibayarSemua,
            'sisa_semua' => $sisaSemua,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    public function togglePartisipasi(Request $request, $siswaId)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatan_tahunan,id',
            'partisipasi' => 'required|in:ikut,tidak_ikut',
            'nisn' => 'required|exists:siswa,nisn'
        ]);

        try {
            \Log::info('Toggle partisipasi data:', $request->all());
            
            $siswa = Siswa::where('nisn', $request->nisn)->firstOrFail();
            
            // Cek apakah siswaId dari route sesuai dengan nisn
            if ($siswa->id != $siswaId) {
                \Log::error('Siswa ID mismatch: route=' . $siswaId . ', nisn=' . $siswa->id);
                Alert::error('Error!', 'Data siswa tidak sesuai');
                return redirect()->route('entri-kegiatan.cari-siswa', ['nisn' => $request->nisn]);
            }

            // Gunakan updateOrCreate untuk memastikan data tersimpan dengan benar
            $siswaKegiatan = SiswaKegiatan::updateOrCreate(
                [
                    'id_siswa' => $siswa->id,
                    'id_kegiatan' => $request->kegiatan_id
                ],
                [
                    'partisipasi' => $request->partisipasi,
                    'alasan_tidak_ikut' => $request->partisipasi === 'tidak_ikut' ? 
                        'Siswa memilih tidak mengikuti kegiatan ini' : null,
                    'angsuran_ke' => $request->partisipasi === 'tidak_ikut' ? 0 : DB::raw('angsuran_ke'),
                    'jumlah_bayar' => $request->partisipasi === 'tidak_ikut' ? 0 : DB::raw('jumlah_bayar'),
                    'is_lunas' => $request->partisipasi === 'tidak_ikut' ? true : DB::raw('is_lunas'),
                    'tgl_bayar' => $request->partisipasi === 'tidak_ikut' ? null : DB::raw('tgl_bayar')
                ]
            );

            \Log::info('Partisipasi berhasil diupdate:', $siswaKegiatan->toArray());
            
            Alert::success('Berhasil!', 'Status partisipasi berhasil diperbarui');
            return redirect()->route('entri-kegiatan.cari-siswa', ['nisn' => $request->nisn]);

        } catch (\Exception $e) {
            \Log::error('Error updating partisipasi: '.$e->getMessage());
            \Log::error('Stack trace: '.$e->getTraceAsString());
            Alert::error('Error!', 'Terjadi kesalahan saat memperbarui status partisipasi: ' . $e->getMessage());
            return redirect()->route('entri-kegiatan.cari-siswa', ['nisn' => $request->nisn]);
        }
    }

    public function bayarSemua(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswa,id',
            'nisn' => 'required|exists:siswa,nisn',
            'jumlah_bayar_semua' => 'required|numeric|min:1',
            'tgl_bayar_semua' => 'required|date',
            'total_sisa' => 'required|numeric'
        ], [
            'required' => 'Field :attribute wajib diisi',
            'min' => 'Pembayaran tidak boleh kurang dari :min'
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->route('entri-kegiatan.cari-siswa', ['nisn' => $request->nisn])
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($request->id_siswa);
            $semuaKegiatan = KegiatanTahunan::all();
            $totalBayar = $request->jumlah_bayar_semua;
            $totalSisa = $request->total_sisa;
            
            // Hitung kembalian
            $kembalian = max(0, $totalBayar - $totalSisa);
            $sisaDibayarkan = $totalBayar - $kembalian;

            // Distribusikan pembayaran ke setiap kegiatan yang belum lunas
            foreach ($semuaKegiatan as $kegiatan) {
                // Cek status partisipasi
                $partisipasi = $siswa->kegiatanSiswa
                    ->where('id_kegiatan', $kegiatan->id)
                    ->first();
                
                $statusPartisipasi = $partisipasi ? $partisipasi->partisipasi : 'ikut';
                
                // Skip jika tidak ikut
                if ($statusPartisipasi === 'tidak_ikut') {
                    continue;
                }

                // Hitung sisa pembayaran untuk kegiatan ini
                $totalDibayar = $siswa->kegiatanSiswa
                    ->where('id_kegiatan', $kegiatan->id)
                    ->where('partisipasi', 'ikut')
                    ->sum('jumlah_bayar');
                
                $sisaPembayaran = max($kegiatan->nominal - $totalDibayar, 0);
                
                // Jika sudah lunas, skip
                if ($sisaPembayaran <= 0) {
                    continue;
                }

                // Jika masih ada sisa dana untuk dibayarkan
                if ($sisaDibayarkan > 0) {
                    $jumlahBayarKegiatan = min($sisaPembayaran, $sisaDibayarkan);
                    
                    // Hitung angsuran keberapa
                    $angsuranKe = SiswaKegiatan::where('id_siswa', $siswa->id)
                        ->where('id_kegiatan', $kegiatan->id)
                        ->count() + 1;

                    // Tentukan apakah lunas untuk pembayaran ini
                    $isLunasPembayaran = ($jumlahBayarKegiatan >= $sisaPembayaran);

                    // Buat pembayaran
                    $pembayaran = SiswaKegiatan::create([
                        'id_siswa' => $siswa->id,
                        'id_kegiatan' => $kegiatan->id,
                        'partisipasi' => 'ikut',
                        'angsuran_ke' => $angsuranKe,
                        'jumlah_bayar' => $jumlahBayarKegiatan,
                        'tgl_bayar' => $request->tgl_bayar_semua,
                        'is_lunas' => $isLunasPembayaran,
                        'kembalian' => 0, // Kembalian dihitung di akhir untuk semua
                    ]);

                    // Update status lunas untuk kegiatan ini
                    $this->updateLunasStatus($siswa->id, $kegiatan->id);
                    
                    $sisaDibayarkan -= $jumlahBayarKegiatan;
                }
            }

            // Jika ada kembalian, tambahkan ke tabungan dan TAMPILKAN ALERT
            if ($kembalian > 0) {
                $saldo_terakhir = Tabungan::where('id_siswa', $siswa->id)
                    ->latest()
                    ->first();
                
                $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
                $saldo_sekarang = $saldo_sebelumnya + $kembalian;

                Tabungan::create([
                    'id_siswa' => $siswa->id,
                    'id_petugas' => auth()->id(),
                    'debit' => $kembalian,
                    'kredit' => 0,
                    'saldo' => $saldo_sekarang,
                    'keterangan' => 'Kembalian pembayaran sekaligus semua kegiatan',
                ]);

                // TAMBAHKAN FLASH DATA UNTUK MENAMPILKAN KEMBALIAN
                session()->flash('kembalian_info', [
                    'jumlah' => $kembalian,
                    'message' => 'Pembayaran berhasil! Kembalian Rp ' . number_format($kembalian, 0, ',', '.') . ' telah ditambahkan ke tabungan.'
                ]);
            }

            DB::commit();

            Alert::success('Berhasil!', 'Pembayaran sekaligus untuk semua kegiatan berhasil disimpan!' . ($kembalian > 0 ? ' Kembalian telah ditambahkan ke tabungan.' : ''));
            return redirect()->route('entri-kegiatan.cari-siswa', ['nisn' => $siswa->nisn]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pembayaran sekaligus: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat menyimpan pembayaran sekaligus');
            return redirect()->route('entri-kegiatan.cari-siswa', ['nisn' => $request->nisn])
                ->withInput();
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_siswa' => 'required|exists:siswa,id',
            'nisn' => 'required|exists:siswa,nisn',
            'id_kegiatan' => 'required|exists:kegiatan_tahunan,id',
            'jumlah_bayar' => 'required|numeric|min:1',
            'tgl_bayar' => 'required|date',
            'jumlah_tagihan' => 'required|numeric'
        ], [
            'required' => 'Field :attribute wajib diisi',
            'min' => 'Pembayaran tidak boleh kurang dari :min'
        ]);

        if ($validator->fails()) {
            return redirect()
                    ->route('entri-kegiatan.cari-siswa', ['nisn' => $request->nisn])
                    ->withErrors($validator)
                    ->withInput();
        }

        try {
            DB::beginTransaction();

            $siswa = Siswa::findOrFail($request->id_siswa);
            $kegiatan = KegiatanTahunan::findOrFail($request->id_kegiatan);
            
            // CEK DULU apakah sudah ada record untuk siswa dan kegiatan ini
            $existingRecord = SiswaKegiatan::where('id_siswa', $request->id_siswa)
                ->where('id_kegiatan', $request->id_kegiatan)
                ->where('partisipasi', 'ikut')
                ->first();

            if ($existingRecord) {
                // Jika sudah ada, UPDATE record yang existing
                $totalDibayarSebelumnya = SiswaKegiatan::where('id_siswa', $request->id_siswa)
                    ->where('id_kegiatan', $request->id_kegiatan)
                    ->sum('jumlah_bayar');
                
                $totalDibayarSekarang = $totalDibayarSebelumnya + $request->jumlah_bayar;
                $isLunas = ($totalDibayarSekarang >= $kegiatan->nominal);
                
                // Hitung kembalian
                $kembalian = $request->jumlah_bayar - $request->jumlah_tagihan;
                $isLunas = $kembalian >= 0;

                // Hitung angsuran keberapa
                $angsuranKe = SiswaKegiatan::where('id_siswa', $request->id_siswa)
                    ->where('id_kegiatan', $request->id_kegiatan)
                    ->count() + 1;

                // Buat pembayaran baru (angsuran)
                $pembayaran = SiswaKegiatan::create([
                    'id_siswa' => $request->id_siswa,
                    'id_kegiatan' => $request->id_kegiatan,
                    'partisipasi' => 'ikut',
                    'angsuran_ke' => $angsuranKe,
                    'jumlah_bayar' => $request->jumlah_bayar,
                    'tgl_bayar' => $request->tgl_bayar,
                    'is_lunas' => $isLunas,
                    'kembalian' => $isLunas ? $kembalian : 0,
                    'id_petugas' => auth()->id(),
                ]);

            } else {
                // Jika belum ada, buat record baru
                $isLunas = ($request->jumlah_bayar >= $kegiatan->nominal);
                $kembalian = $request->jumlah_bayar - $kegiatan->nominal;
                
                $pembayaran = SiswaKegiatan::create([
                    'id_siswa' => $request->id_siswa,
                    'id_kegiatan' => $request->id_kegiatan,
                    'partisipasi' => 'ikut',
                    'angsuran_ke' => 1,
                    'jumlah_bayar' => $request->jumlah_bayar,
                    'tgl_bayar' => $request->tgl_bayar,
                    'is_lunas' => $isLunas,
                    'kembalian' => $isLunas ? $kembalian : 0,
                    'id_petugas' => auth()->id(),
                ]);
            }

            // Jika ada kembalian, tambahkan ke tabungan
            if ($isLunas && $kembalian > 0) {
                $saldo_terakhir = Tabungan::where('id_siswa', $request->id_siswa)
                    ->latest()
                    ->first();
                
                $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
                $saldo_sekarang = $saldo_sebelumnya + $kembalian;

                Tabungan::create([
                    'id_siswa' => $request->id_siswa,
                    'id_pembayaran_kegiatan' => $pembayaran->id,
                    'id_petugas' => auth()->id(),
                    'debit' => $kembalian,
                    'kredit' => 0,
                    'saldo' => $saldo_sekarang,
                    'keterangan' => 'Kembalian pembayaran kegiatan ' . $kegiatan->nama_kegiatan . ' angsuran ke-' . ($existingRecord ? $angsuranKe : 1),
                ]);
            }

            // Update status lunas untuk semua record kegiatan ini
            $this->updateLunasStatus($request->id_siswa, $request->id_kegiatan);

            DB::commit();

            Alert::success('Berhasil!', 'Pembayaran kegiatan berhasil disimpan!');
            return redirect()->route('entri-kegiatan.cari-siswa', ['nisn' => $siswa->nisn]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating pembayaran kegiatan: '.$e->getMessage());
            Alert::error('Error!', 'Terjadi kesalahan saat menyimpan pembayaran kegiatan');
            return redirect()->route('entri-kegiatan.cari-siswa', ['nisn' => $request->nisn])
                ->withInput();
        }
    }

    public function edit($id)
    {
        $pembayaran = SiswaKegiatan::with(['siswa', 'kegiatan'])->findOrFail($id);
        
        return view('dashboard.entri-kegiatan.edit', [
            'edit' => $pembayaran,
            'user' => User::find(auth()->user()->id)
        ]);
    }

    public function update(Request $request, $id)
    {
        \Log::info('Update method called', ['id' => $id, 'request' => $request->all()]);
        
        try {
            $pembayaran = SiswaKegiatan::with(['siswa', 'kegiatan'])->findOrFail($id);
            \Log::info('Pembayaran found', ['pembayaran' => $pembayaran->toArray()]);
            
            $validator = Validator::make($request->all(), [
                'angsuran_ke' => 'required|numeric|min:1',
                'jumlah_bayar' => 'required|numeric|min:1',
                'tgl_bayar' => 'required|date',
            ], [
                'required' => 'Field :attribute wajib diisi',
                'min' => ':attribute tidak boleh kurang dari :min'
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed', ['errors' => $validator->errors()->toArray()]);
                return redirect()
                        ->back()
                        ->withErrors($validator)
                        ->withInput();
            }

            DB::beginTransaction();
            \Log::info('Transaction started');

            // Hitung jumlah_tagihan dari kegiatan, bukan dari input
            $jumlah_tagihan = $pembayaran->kegiatan->nominal;
            $kembalian = $request->jumlah_bayar - $jumlah_tagihan;
            $isLunas = $kembalian >= 0;

            \Log::info('Calculation', [
                'jumlah_tagihan' => $jumlah_tagihan,
                'jumlah_bayar' => $request->jumlah_bayar,
                'kembalian' => $kembalian,
                'isLunas' => $isLunas
            ]);

            $pembayaran->update([
                'angsuran_ke' => $request->angsuran_ke,
                'jumlah_bayar' => $request->jumlah_bayar,
                'tgl_bayar' => $request->tgl_bayar,
                'is_lunas' => $isLunas,
                'kembalian' => $isLunas ? $kembalian : 0,
                'id_petugas' => auth()->id(),
            ]);

            \Log::info('Pembayaran updated');

            // Update status lunas
            $this->updateLunasStatus($pembayaran->id_siswa, $pembayaran->id_kegiatan);
            \Log::info('Lunas status updated');

            // Kelola tabungan untuk kembalian
            $tabungan = Tabungan::where('id_pembayaran_kegiatan', $pembayaran->id)->first();
            $kembalian_lama = $pembayaran->getOriginal('kembalian') ?? 0;
            
            \Log::info('Tabungan check', [
                'tabungan_exists' => !is_null($tabungan),
                'kembalian_lama' => $kembalian_lama,
                'kembalian_baru' => $kembalian
            ]);
            
            if ($isLunas && $kembalian > 0) {
                if ($tabungan) {
                    $tabungan->update([
                        'debit' => $kembalian,
                        'saldo' => $tabungan->saldo - $kembalian_lama + $kembalian,
                        'keterangan' => 'Kembalian pembayaran kegiatan ' . $pembayaran->kegiatan->nama_kegiatan . ' angsuran ke-' . $request->angsuran_ke,
                    ]);
                    \Log::info('Tabungan updated');
                } else {
                    $saldo_terakhir = Tabungan::where('id_siswa', $pembayaran->id_siswa)
                        ->latest()
                        ->first();
                    
                    $saldo_sebelumnya = $saldo_terakhir ? $saldo_terakhir->saldo : 0;
                    $saldo_sekarang = $saldo_sebelumnya + $kembalian;

                    Tabungan::create([
                        'id_siswa' => $pembayaran->id_siswa,
                        'id_pembayaran_kegiatan' => $pembayaran->id,
                        'id_petugas' => auth()->id(),
                        'debit' => $kembalian,
                        'kredit' => 0,
                        'saldo' => $saldo_sekarang,
                        'keterangan' => 'Kembalian pembayaran kegiatan ' . $pembayaran->kegiatan->nama_kegiatan . ' angsuran ke-' . $request->angsuran_ke,
                    ]);
                    \Log::info('New tabungan created');
                }
            } elseif ($tabungan) {
                $tabungan->delete();
                \Log::info('Tabungan deleted');
            }

            DB::commit();
            \Log::info('Transaction committed');

            Alert::success('Berhasil!', 'Pembayaran kegiatan berhasil diperbarui');
            return redirect()->route('entri-kegiatan.index');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in update method: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            Alert::error('Error!', 'Terjadi kesalahan saat memperbarui pembayaran kegiatan: ' . $e->getMessage());
            return back()->withInput();
        }
    }

    private function updateLunasStatus($idSiswa, $idKegiatan)
    {
        $totalDibayar = SiswaKegiatan::where('id_siswa', $idSiswa)
            ->where('id_kegiatan', $idKegiatan)
            ->sum('jumlah_bayar');
        
        $totalTagihan = KegiatanTahunan::find($idKegiatan)->nominal ?? 0;
        $isLunas = ($totalDibayar >= $totalTagihan);
        
        if ($isLunas) {
            SiswaKegiatan::where('id_siswa', $idSiswa)
                ->where('id_kegiatan', $idKegiatan)
                ->update(['is_lunas' => true]);
        } else {
            SiswaKegiatan::where('id_siswa', $idSiswa)
                ->where('id_kegiatan', $idKegiatan)
                ->update(['is_lunas' => false]);
        }
        
        return $isLunas;
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction(); // Mulai transaction
            
            $pembayaran = SiswaKegiatan::findOrFail($id);
            $idSiswa = $pembayaran->id_siswa;
            $idKegiatan = $pembayaran->id_kegiatan;
            
            // Hapus data tabungan terkait jika ada
            $tabungan = Tabungan::where('id_pembayaran_kegiatan', $pembayaran->id)->first();
            if ($tabungan) {
                $tabungan->delete();
            }
            
            if($pembayaran->delete()) {
                $this->updateLunasStatus($idSiswa, $idKegiatan);
                
                Alert::success('Berhasil!', 'Pembayaran kegiatan berhasil dihapus!');
            } else {
                Alert::error('Terjadi Kesalahan!', 'Pembayaran kegiatan gagal dihapus!');
            }
            
            DB::commit(); // Commit transaction
            
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction jika error
            Alert::error('Terjadi Kesalahan!', 'Pembayaran kegiatan gagal dihapus!');
        }
        
        return back();
    }

    public function generatePDF($id)
    {
        ini_set('max_execution_time', 300);
        
        $user = Auth::user();
        $tanggal = Carbon::now()->format('d-m-Y');
        $pembayaran = SiswaKegiatan::with(['siswa', 'siswa.kelas', 'kegiatan'])->findOrFail($id);

        // Pastikan path gambar sesuai dengan struktur project Anda
        $logoPath = public_path('img/amanah31.png');
        $websitePath = public_path('img/icons/website.png');
        $instagramPath = public_path('img/icons/instagram.png');
        $facebookPath = public_path('img/icons/facebook.png');
        $youtubePath = public_path('img/icons/youtube.png');
        $whatsappPath = public_path('img/icons/whatsapp.png');
        $barcodePath = public_path('img/barcode/barcode-ita.png');

        // Convert images to base64
        $logoData = base64_encode(file_get_contents($logoPath));
        $websiteData = base64_encode(file_get_contents($websitePath));
        $instagramData = base64_encode(file_get_contents($instagramPath));
        $facebookData = base64_encode(file_get_contents($facebookPath));
        $youtubeData = base64_encode(file_get_contents($youtubePath));
        $whatsappData = base64_encode(file_get_contents($whatsappPath));
        $barcodeData = base64_encode(file_get_contents($barcodePath));

        $pdf = PDF::loadView('pdf.bukti-kegiatan', compact(
            'pembayaran', 
            'logoData', 
            'websiteData', 
            'instagramData', 
            'facebookData', 
            'youtubeData', 
            'whatsappData', 
            'barcodeData', 
            'user'
        ))
        ->setPaper('a5', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        $namaFile = 'Bukti-Pembayaran-Kegiatan-' . $pembayaran->siswa->nama . '-' . $tanggal . '.pdf';
        
        return $pdf->download($namaFile);
    }

    public function generateRekapSiswaPDF(Request $request)
    {
        ini_set('max_execution_time', 300);
        
        $user = Auth::user();
        $tanggal = Carbon::now()->format('d-m-Y');

        // Validasi NISN
        $request->validate([
            'nisn' => 'required|exists:siswa,nisn'
        ]);

        // Ambil data siswa dan pembayaran
        $siswa = Siswa::with(['kelas', 'kegiatanSiswa.kegiatan'])->where('nisn', $request->nisn)->firstOrFail();
        
        $pembayaran = SiswaKegiatan::with(['kegiatan'])
            ->where('id_siswa', $siswa->id)
            ->where('partisipasi', 'ikut')
            ->orderBy('tgl_bayar', 'asc')
            ->get();

        // Hitung total seperti di method cariSiswa
        $semuaKegiatan = KegiatanTahunan::all();
        $detailKegiatan = [];
        $totalTagihanKegiatan = 0; // PERBAIKAN: Gunakan camelCase
        $totalDibayarSemua = 0;    // PERBAIKAN: Gunakan camelCase
        $sisaSemua = 0;            // PERBAIKAN: Gunakan camelCase

        foreach ($semuaKegiatan as $kegiatan) {
            $partisipasi = $siswa->kegiatanSiswa
                ->where('id_kegiatan', $kegiatan->id)
                ->first();
            
            $statusPartisipasi = $partisipasi ? $partisipasi->partisipasi : 'ikut';
            $alasanTidakIkut = $partisipasi ? $partisipasi->alasan_tidak_ikut : null;

            if ($statusPartisipasi === 'tidak_ikut') {
                $detailKegiatan[] = [
                    'kegiatan' => $kegiatan,
                    'partisipasi' => 'tidak_ikut',
                    'alasan_tidak_ikut' => $alasanTidakIkut,
                    'total_dibayar' => 0,
                    'sisa_pembayaran' => 0,
                    'is_lunas' => true
                ];
                continue;
            }

            $totalDibayar = $siswa->kegiatanSiswa
                ->where('id_kegiatan', $kegiatan->id)
                ->where('partisipasi', 'ikut')
                ->sum('jumlah_bayar');
            
            $sisaPembayaran = max($kegiatan->nominal - $totalDibayar, 0);
            $isLunas = ($totalDibayar >= $kegiatan->nominal);

            $detailKegiatan[] = [
                'kegiatan' => $kegiatan,
                'partisipasi' => 'ikut',
                'alasan_tidak_ikut' => null,
                'total_dibayar' => $totalDibayar,
                'sisa_pembayaran' => $sisaPembayaran,
                'is_lunas' => $isLunas
            ];

            // Hanya hitung jika siswa ikut kegiatan
            if ($statusPartisipasi === 'ikut') {
                $totalTagihanKegiatan += $kegiatan->nominal;
                $totalDibayarSemua += $totalDibayar;
            }
        }

        $sisaSemua = max($totalTagihanKegiatan - $totalDibayarSemua, 0);

            $logoPath = public_path('img/amanah31.png');
            $websitePath = public_path('img/icons/website.png');
            $instagramPath = public_path('img/icons/instagram.png');
            $facebookPath = public_path('img/icons/facebook.png');
            $youtubePath = public_path('img/icons/youtube.png');
            $whatsappPath = public_path('img/icons/whatsapp.png');
            $barcodePath = public_path('img/barcode/barcode-ita.png');

            $logoData = base64_encode(file_get_contents($logoPath));
            $websiteData = base64_encode(file_get_contents($websitePath));
            $instagramData = base64_encode(file_get_contents($instagramPath));
            $facebookData = base64_encode(file_get_contents($facebookPath));
            $youtubeData = base64_encode(file_get_contents($youtubePath));
            $whatsappData = base64_encode(file_get_contents($whatsappPath));
            $barcodeData = base64_encode(file_get_contents($barcodePath));

        $pdf = PDF::loadView('pdf.rekap-siswa-kegiatan', compact(
            'pembayaran',
            'siswa',
            'detailKegiatan',
            'totalTagihanKegiatan',  
            'totalDibayarSemua',     
            'sisaSemua',             
            'logoData',
            'barcodeData',
            'websiteData',
            'instagramData',
            'facebookData',
            'youtubeData',
            'whatsappData',
            'user'
        ))
        ->setPaper('a5', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        $namaFile = 'Rekap-Pembayaran-Kegiatan-' . $siswa->nama . '-' . $tanggal . '.pdf';
        
        return $pdf->download($namaFile);
    }
}