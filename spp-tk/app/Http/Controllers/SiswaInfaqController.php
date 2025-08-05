<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AngsuranInfaq;
use App\Models\Pembayaran;
use App\Models\Tabungan;
use App\Models\Siswa;
use PDF;
use Carbon\Carbon;

class SiswaInfaqController extends Controller
{
    /**
     * Generate PDF bukti pembayaran infaq untuk siswa
     */
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

        $namaFile = 'Bukti-Pembayaran-SPP-' . $pembayaran->siswa->nama . '-' . $tanggal . '.pdf';
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

    /**
     * Cetak rekap tabungan siswa
     */
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

        $query = Tabungan::with('petugas')->where('id_siswa', $siswa->id)->orderBy('created_at', 'ASC');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $tabungan = $query->get();

        $saldo = Tabungan::where('id_siswa', $siswa->id)->latest()->first()->saldo ?? 0;

        $logoData = base64_encode(file_get_contents(public_path('img/amanah31.png')));
        $websiteData = base64_encode(file_get_contents(public_path('img/icons/website.png')));
        $instagramData = base64_encode(file_get_contents(public_path('img/icons/instagram.png')));
        $facebookData = base64_encode(file_get_contents(public_path('img/icons/facebook.png')));
        $youtubeData = base64_encode(file_get_contents(public_path('img/icons/youtube.png')));
        $whatsappData = base64_encode(file_get_contents(public_path('img/icons/whatsapp.png')));
        $barcodeData = base64_encode(file_get_contents(public_path('img/barcode/barcode-ita.png')));

        $pdf = PDF::loadView('pdf.tabungan', compact(
            'siswa',
            'tabungan',
            'saldo',
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

        $namaFile = 'Rekap-Tabungan-' . $siswa->nama . '-' . $tanggal . '.pdf';
        return $pdf->download($namaFile);
    }
}