<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AngsuranInfaq;
use App\Models\Pembayaran;
use App\Models\Tabungan;
use App\Models\Siswa;
use App\Models\UangTahunan;
use App\Models\SiswaKegiatan;
use App\Models\KegiatanTahunan;
use PDF;
use Carbon\Carbon;

class SiswaInfaqController extends Controller
{

    public function generateInfaq($id)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');
        
        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $angsuran = AngsuranInfaq::with(['siswa', 'infaqGedung'])
                        ->whereHas('siswa', function($query) use ($nisn) {
                            $query->where('nisn', $nisn);
                        })
                        ->findOrFail($id);

        $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
        $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
        $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
        $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
        $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
        $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
        $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));

        $pdf = PDF::loadView('pdf.bukti-infaq', compact(
            'angsuran',
            'logoData',
            'websiteData',
            'instagramData',
            'facebookData',
            'youtubeData',
            'whatsappData',
            'barcodeData'
        ))  ->setPaper('a5', 'portrait')
            ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                      'dpi' => 150
                  ]);

        $namaFile = 'Bukti-Pembayaran-SPP-' . $angsuran->siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    public function generateRekapInfaq(Request $request)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');

        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $siswa = Siswa::with(['kelas', 'infaqGedung'])->where('nisn', $nisn)->firstOrFail();
        
        $riwayatInfaq = AngsuranInfaq::where('id_siswa', $siswa->id)
            ->with(['petugas', 'infaqGedung'])
            ->orderBy('tgl_bayar', 'desc')
            ->orderBy('angsuran_ke', 'desc')
            ->get();

        $totalDibayar = $riwayatInfaq->sum('jumlah_bayar');
        $totalTagihan = $siswa->infaqGedung->nominal ?? 0;
        $totalKembalian = $riwayatInfaq->sum('kembalian');
        $sisaPembayaran = max(0, $totalTagihan - $totalDibayar);

        try {
            $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
            $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
            $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
            $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
            $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
            $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
            $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));
        } catch (\Exception $e) {
            $logoData = '';
            $websiteData = '';
            $instagramData = '';
            $facebookData = '';
            $youtubeData = '';
            $whatsappData = '';
            $barcodeData = '';
        }

        $pdf = PDF::loadView('pdf.rekap-pembayaran-infaq', [
            'siswa' => $siswa,
            'riwayatInfaq' => $riwayatInfaq,
            'logoData' => $logoData,
            'websiteData' => $websiteData,
            'instagramData' => $instagramData,
            'facebookData' => $facebookData,
            'youtubeData' => $youtubeData,
            'whatsappData' => $whatsappData,
            'barcodeData' => $barcodeData,
            'tanggal' => $tanggal,
            'totalDibayar' => $totalDibayar,
            'totalTagihan' => $totalTagihan,
            'totalKembalian' => $totalKembalian,
            'sisaPembayaran' => $sisaPembayaran
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        $namaFile = 'Rekap-Pembayaran-Infaq-' . $siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    public function generateSpp($id)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');
        
        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $pembayaran = Pembayaran::with(['siswa.kelas', 'petugas'])
                        ->whereHas('siswa', function($query) use ($nisn) {
                            $query->where('nisn', $nisn);
                        })
                        ->findOrFail($id);

        $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
        $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
        $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
        $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
        $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
        $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
        $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));

        $pdf = PDF::loadView('pdf.bukti', compact(
            'pembayaran',
            'logoData',
            'websiteData',
            'instagramData',
            'facebookData',
            'youtubeData',
            'whatsappData',
            'barcodeData'
        ))
        ->setPaper('a5', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150
        ]);

        $namaFile = 'Bukti-Pembayaran-SPP-' . $pembayaran->siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    public function generateRekapSpp(Request $request)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');

        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $siswa = Siswa::with('kelas')->where('nisn', $nisn)->firstOrFail();
        
        $riwayatPembayaran = Pembayaran::where('id_siswa', $siswa->id)
            ->with('petugas')
            ->orderBy('tahun', 'desc')
            ->orderByRaw("FIELD(bulan, 'januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember') DESC")
            ->get();

        try {
            $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
            $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
            $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
            $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
            $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
            $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
            $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));
        } catch (\Exception $e) {
            $logoData = '';
            $websiteData = '';
            $instagramData = '';
            $facebookData = '';
            $youtubeData = '';
            $whatsappData = '';
            $barcodeData = '';
        }

        $pdf = PDF::loadView('pdf.rekap-pembayaran-spp', [
            'siswa' => $siswa,
            'riwayatPembayaran' => $riwayatPembayaran,
            'logoData' => $logoData,
            'websiteData' => $websiteData,
            'instagramData' => $instagramData,
            'facebookData' => $facebookData,
            'youtubeData' => $youtubeData,
            'whatsappData' => $whatsappData,
            'barcodeData' => $barcodeData,
            'tanggal' => $tanggal
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        $namaFile = 'Rekap-Pembayaran-SPP-' . $siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    public function generateTabungan(Request $request)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');

        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $siswa = Siswa::with('kelas')->where('nisn', $nisn)->firstOrFail();

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : null;

        $query = Tabungan::with('petugas')
                    ->where('id_siswa', $siswa->id)
                    ->orderBy('created_at', 'DESC'); 

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $tabungan = $query->get();

        try {
            $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
            $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
            $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
            $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
            $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
            $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
            $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));
        } catch (\Exception $e) {
            $logoData = '';
            $websiteData = '';
            $instagramData = '';
            $facebookData = '';
            $youtubeData = '';
            $whatsappData = '';
            $barcodeData = '';
        }

        $pdf = PDF::loadView('pdf.rekap-tabungan', [
            'siswa' => $siswa,
            'tabungan' => $tabungan,
            'logoData' => $logoData,
            'websiteData' => $websiteData,
            'instagramData' => $instagramData,
            'facebookData' => $facebookData,
            'youtubeData' => $youtubeData,
            'whatsappData' => $whatsappData,
            'barcodeData' => $barcodeData,
            'tanggal' => now()->format('d F Y')
        ])->setPaper('a4', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        $namaFile = 'Rekap-Tabungan-' . $siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    public function generateTabunganSingle($id)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');
        
        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $transaksi = Tabungan::with(['siswa.kelas', 'petugas'])
                        ->whereHas('siswa', function($query) use ($nisn) {
                            $query->where('nisn', $nisn);
                        })
                        ->findOrFail($id);

        $siswa = $transaksi->siswa;

        $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
        $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
        $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
        $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
        $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
        $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
        $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));

        $pdf = PDF::loadView('pdf.bukti-tabungan', compact(
            'transaksi',
            'siswa',
            'logoData',
            'websiteData',
            'instagramData',
            'facebookData',
            'youtubeData',
            'whatsappData',
            'barcodeData'
        ))
        ->setPaper('a5', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150
        ]);

        $jenis = $transaksi->debit > 0 ? 'Setoran' : 'Penarikan';
        $namaFile = 'Bukti-' . $jenis . '-Tabungan-' . $siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }


    public function generateKegiatan($id)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');
        
        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $pembayaran = SiswaKegiatan::with(['siswa.kelas', 'kegiatan', 'petugas'])
                        ->whereHas('siswa', function($query) use ($nisn) {
                            $query->where('nisn', $nisn);
                        })
                        ->findOrFail($id);

        try {
            $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
            $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
            $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
            $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
            $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
            $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
            $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));
        } catch (\Exception $e) {
            $logoData = '';
            $websiteData = '';
            $instagramData = '';
            $facebookData = '';
            $youtubeData = '';
            $whatsappData = '';
            $barcodeData = '';
        }

        $pdf = PDF::loadView('pdf.bukti-kegiatan', [
            'pembayaran' => $pembayaran,
            'logoData' => $logoData,
            'websiteData' => $websiteData,
            'instagramData' => $instagramData,
            'facebookData' => $facebookData,
            'youtubeData' => $youtubeData,
            'whatsappData' => $whatsappData,
            'barcodeData' => $barcodeData,
        ])
        ->setPaper('a5', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        $namaFile = 'Bukti-Pembayaran-Kegiatan-' . $pembayaran->siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    public function generateRekapKegiatan(Request $request)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');

        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $siswa = Siswa::with(['kelas', 'kegiatanSiswa.kegiatan', 'paketKegiatan'])->where('nisn', $nisn)->firstOrFail();
        
        if ($siswa->id_paket_kegiatan && $siswa->paketKegiatan) {
            $semuaKegiatan = KegiatanTahunan::where('nama_paket', $siswa->paketKegiatan->nama_paket)
                ->whereNotNull('nama_kegiatan')
                ->get();
        } else {
            $semuaKegiatan = KegiatanTahunan::whereNotNull('nama_kegiatan')->get();
        }

        $pembayaran = SiswaKegiatan::with(['kegiatan'])
            ->where('id_siswa', $siswa->id)
            ->where('partisipasi', 'ikut')
            ->orderBy('tgl_bayar', 'asc')
            ->get();

        $detailKegiatan = [];
        $totalTagihanKegiatan = 0; 
        $totalDibayarSemua = 0;    
        $sisaSemua = 0;            

        foreach ($semuaKegiatan as $kegiatan) {
            if (empty($kegiatan->nama_kegiatan)) {
                continue;
            }

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

            if ($statusPartisipasi === 'ikut') {
                $totalTagihanKegiatan += $kegiatan->nominal;
                $totalDibayarSemua += $totalDibayar;
            }
        }

        $sisaSemua = max($totalTagihanKegiatan - $totalDibayarSemua, 0);

        try {
            $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
            $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
            $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
            $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
            $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
            $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
            $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));
        } catch (\Exception $e) {
            $logoData = '';
            $websiteData = '';
            $instagramData = '';
            $facebookData = '';
            $youtubeData = '';
            $whatsappData = '';
            $barcodeData = '';
        }

        $userObject = (object) [
            'name' => 'Sistem Siswa',
            'role' => 'Siswa'
        ];

        $pdf = PDF::loadView('pdf.rekap-siswa-kegiatan', [
            'pembayaran' => $pembayaran,
            'siswa' => $siswa,
            'detailKegiatan' => $detailKegiatan,
            'totalTagihanKegiatan' => $totalTagihanKegiatan,  
            'totalDibayarSemua' => $totalDibayarSemua,     
            'sisaSemua' => $sisaSemua,             
            'logoData' => $logoData,
            'barcodeData' => $barcodeData,
            'websiteData' => $websiteData,
            'instagramData' => $instagramData,
            'facebookData' => $facebookData,
            'youtubeData' => $youtubeData,
            'whatsappData' => $whatsappData,
            'user' => $userObject
        ])
        ->setPaper('a5', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150,
        ]);

        $namaFile = 'Rekap-Pembayaran-Kegiatan-' . $siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }

    public function generateUangTahunan(Request $request)
    {
        $nisn = session('nisn');
        $tanggal = Carbon::now()->format('d-m-Y');

        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        $siswa = Siswa::with('kelas')->where('nisn', $nisn)->firstOrFail();
        
        $tahun = $request->input('tahun', Carbon::now()->year);

        $uangTahunan = UangTahunan::with('petugas')
                        ->where('id_siswa', $siswa->id)
                        ->where('tahun_ajaran', $tahun)
                        ->orderBy('created_at', 'ASC')
                        ->get();

        $saldo = UangTahunan::where('id_siswa', $siswa->id)
                    ->where('tahun_ajaran', $tahun)
                    ->latest()
                    ->first()
                    ->saldo ?? 0;

        $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
        $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
        $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
        $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
        $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
        $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
        $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));

        $pdf = PDF::loadView('pdf.uang-tahunan', compact(
            'siswa',
            'uangTahunan',
            'saldo',
            'tahun',
            'logoData',
            'websiteData',
            'instagramData',
            'facebookData',
            'youtubeData',
            'whatsappData',
            'barcodeData'
        ))
        ->setPaper('a5', 'portrait')
        ->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 150
        ]);

        $namaFile = 'Rekap-Uang-Tahunan-' . $siswa->nama . '-' . $tahun . '.pdf';
        return $pdf->download($namaFile);
    }
}