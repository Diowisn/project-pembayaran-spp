<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Alert;
use App\Models\Pembayaran;
use App\Models\AngsuranInfaq;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\UangTahunan;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'pembayaran' => Pembayaran::with(['siswa.kelas', 'siswa.spp'])
                            ->orderBy('created_at', 'DESC')
                            ->paginate(15),
            'user' => User::find(auth()->user()->id)
        ];
         
        return view('dashboard.history-pembayaran.index', $data);
    }

    /**
     * Display infaq payment history
     *
     * @return \Illuminate\Http\Response
     */
    public function infaq()
    {
        $data = [
            'infaqHistori' => AngsuranInfaq::with(['siswa.kelas', 'infaqGedung'])
                            ->orderBy('created_at', 'DESC')
                            ->paginate(15),
            'user' => User::find(auth()->user()->id),
            'kelasList' => Kelas::all()
        ];
        
        return view('dashboard.history-infaq.index', $data);
    }

    /**
     * Display annual fund payment history
     *
     * @return \Illuminate\Http\Response
     */
    public function uangTahunan(Request $request)
    {
        $query = UangTahunan::with(['siswa.kelas', 'petugas'])
                    ->orderBy('created_at', 'DESC');

        // Filter pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('siswa', function($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        // Filter tahun ajaran
        if ($request->has('tahun') && $request->tahun != '') {
            $query->where('tahun_ajaran', $request->tahun);
        }

        $data = [
            'uangTahunanHistori' => $query->paginate(15),
            'tahunAjaran' => UangTahunan::select('tahun_ajaran')
                                ->distinct()
                                ->orderBy('tahun_ajaran', 'DESC')
                                ->pluck('tahun_ajaran'),
            'user' => User::find(auth()->user()->id),
            'search' => $request->search,
            'tahun' => $request->tahun
        ];
        
        return view('dashboard.history-uang-tahunan.index', $data);
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
