<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AngsuranInfaq;
use App\Models\Pembayaran;
use PDF;

class SiswaInfaqController extends Controller
{
    /**
     * Generate PDF bukti pembayaran infaq untuk siswa
     */
    public function generateInfaq($id)
    {
        // Ambil NISN dari session
        $nisn = session('nisn');
        
        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        // Cari data angsuran
        $angsuran = AngsuranInfaq::with(['siswa', 'infaqGedung'])
                        ->whereHas('siswa', function($query) use ($nisn) {
                            $query->where('nisn', $nisn);
                        })
                        ->findOrFail($id);

        $pdf = PDF::loadView('pdf.bukti-infaq', compact('angsuran'))
                  ->setPaper('a5', 'portrait');
        
        return $pdf->download('Bukti-Pembayaran-Infaq-'.$angsuran->siswa->nama.'.pdf');
    }

    public function generateSpp($id)
    {
        // Ambil NISN dari session
        $nisn = session('nisn');
        
        if (!$nisn) {
            abort(403, 'Akses ditolak - Silakan login terlebih dahulu');
        }

        // Cari data pembayaran SPP
        $pembayaran = Pembayaran::with(['siswa.kelas', 'petugas'])
                        ->whereHas('siswa', function($query) use ($nisn) {
                            $query->where('nisn', $nisn);
                        })
                        ->findOrFail($id);

        $pdf = PDF::loadView('pdf.bukti', compact('pembayaran'))
                  ->setPaper('a5', 'portrait')
                  ->setOptions([
                      'isHtml5ParserEnabled' => true,
                      'isRemoteEnabled' => true,
                      'dpi' => 150
                  ]);
        
        return $pdf->download('Bukti-Pembayaran-SPP-'.$pembayaran->siswa->nama.'.pdf');
    }
}