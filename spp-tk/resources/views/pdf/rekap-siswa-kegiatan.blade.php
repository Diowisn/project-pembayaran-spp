<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rekap Pembayaran Kegiatan per Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            padding: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .header-left {
            text-align: left;
        }

        .header-left img {
            width: 60px;
            height: auto;
            object-fit: contain;
            margin-bottom: 5px;
        }

        .header-left h2 {
            margin: 2px 0;
            font-size: 16px;
        }

        .header-left p {
            margin: 2px 0;
            font-size: 11px;
        }

        .header-right {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .info-table td {
            padding: 5px 0;
        }

        .rincian-title {
            text-align: center;
            font-weight: normal;
            margin: 10px 0;
        }

        .detail-table th,
        .detail-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
        }

        .detail-table th {
            background-color: #f2f2f2;
        }

        .total {
            margin-top: 5px;
            text-align: right;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
        }

        .footer .left {
            float: left;
        }

        .footer .right {
            float: right;
            text-align: center;
        }
        
        .footer .right img {
            width: 100px;
            height: auto;
            margin: 0 auto 5px;
            display: block;
        }
        
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        
        .status-lunas {
            color: green;
            font-weight: bold;
        }
        
        .status-belum {
            color: orange;
            font-weight: bold;
        }
        
        .status-tidak-ikut {
            color: red;
            font-weight: bold;
        }
        
        .student-info {
            margin: 15px 0;
            padding: 10px;
            /* background-color: #f8f9fa; */
            border-radius: 5px;
        }
        
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f5e8;
            border-radius: 5px;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .social-icons {
            margin-top: 5px;
        }
        
        .social-icons img {
            width: 12px;
            height: 12px;
            margin-right: 5px;
        }
        
        .paket-info {
            background-color: #e3f2fd;
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-left">
            <img src="data:image/png;base64,{{ $logoData }}" style="width: 200px; height: auto; display: block; margin-bottom: 10px;" alt="Logo Sekolah">
            <p style="font-size: 12px">Jl. Raya Ngawi-Solo KM 33 Dadung RT. 1/11, Sambirejo, Mantingan, Ngawi</p>
            <p style="margin-top: 6px; font-size: 12px; line-height: 1.6;">
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $websiteData }}" alt="Website"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;"> www.assakiinah.com
                </span>
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $instagramData }}" alt="Instagram"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;"> pas_asskiinah
                </span>
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $facebookData }}" alt="Facebook"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;"> Pas Assakiinah
                </span>
                <span style="margin-right: 3px;">
                    <img src="data:image/png;base64,{{ $youtubeData }}" alt="YouTube"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:2px;"> Assakiinah TV
                </span>
                <span>
                    <img src="data:image/png;base64,{{ $whatsappData }}" alt="WhatsApp"
                        style="width:14px; height:14px; vertical-align: text-bottom; margin-right:4px;"> +62 851 6258 6667
                </span>
            </p>
            <br>
            <br>
        </div>
        <div class="header-right">
            Rekap Pembayaran Kegiatan
        </div>
    </div>

    <!-- Informasi Paket -->
    @if($siswa->paketKegiatan)
    <div class="paket-info">
        <strong>Paket Kegiatan:</strong> {{ $siswa->paketKegiatan->nama_paket }}<br>
        <small>Siswa terdaftar dalam paket kegiatan ini</small>
    </div>
    @endif

    <div class="student-info">
        <table class="info-table">
            <tr>
                <td width="20%">NISN</td>
                <td width="30%">: {{ $siswa->nisn }}</td>
                <td width="20%">Kelas</td>
                <td width="30%">: {{ $siswa->kelas->nama_kelas ?? '-' }}</td>
            </tr>
            <tr>
                <td>Nama Siswa</td>
                <td>: {{ $siswa->nama }}</td>
                <td>Tahun Ajaran</td>
                <td>: {{ date('Y') }}/{{ date('Y') + 1 }}</td>
            </tr>
            <tr>
                <td>Total Tagihan</td>
                <td>: Rp {{ number_format($totalTagihanKegiatan, 0, ',', '.') }}</td>
                <td>Total Dibayar</td>
                <td>: Rp {{ number_format($totalDibayarSemua, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Sisa Pembayaran</td>
                <td>: Rp {{ number_format($sisaSemua, 0, ',', '.') }}</td>
                <td>Status</td>
                <td>: 
                    @if($sisaSemua == 0)
                        <span class="status-lunas">LUNAS SEMUA</span>
                    @else
                        <span class="status-belum">BELUM LUNAS</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Tabel Detail Pembayaran -->
    @if($pembayaran->count() > 0)
    <p class="rincian-title">Daftar Pembayaran Kegiatan:</p>

    <table class="detail-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal Bayar</th>
                <th>Kegiatan</th>
                <th width="8%">Angsuran</th>
                <th width="12%">Nominal</th>
                <th width="12%">Jumlah Bayar</th>
                <th width="10%">Kembalian</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php
                $counter = 1;
                $totalBayar = 0;
                $totalKembalian = 0;
            @endphp

            @foreach($pembayaran as $item)
                @if($item->kegiatan && !empty($item->kegiatan->nama_kegiatan))
                <tr>
                    <td class="text-center">{{ $counter++ }}</td>
                    <td>
                        @if($item->tgl_bayar)
                            {{ $item->tgl_bayar->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->kegiatan->nama_kegiatan }}</td>
                    <td class="text-center">{{ $item->angsuran_ke }}</td>
                    <td class="text-right">Rp {{ number_format($item->kegiatan->nominal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($item->kembalian > 0)
                            Rp {{ number_format($item->kembalian, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->is_lunas)
                            <span class="status-lunas">LUNAS</span>
                        @else
                            <span class="status-belum">BELUM</span>
                        @endif
                    </td>
                </tr>
                @php
                    $totalBayar += $item->jumlah_bayar;
                    $totalKembalian += $item->kembalian;
                @endphp
                @endif
            @endforeach

            <!-- Total -->
            @if($counter > 1)
            <tr style="background-color: #e8f5e8; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                <td class="text-right">
                    @if($totalKembalian > 0)
                        Rp {{ number_format($totalKembalian, 0, ',', '.') }}
                    @else
                        -
                    @endif
                </td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>
    @else
    <div style="text-align: center; padding: 20px; background-color: #f8f9fa; border-radius: 5px;">
        <p>Belum ada data pembayaran kegiatan</p>
    </div>
    @endif

    <!-- Ringkasan Status Kegiatan -->
    @if(count($detailKegiatan) > 0)
    <div class="summary">
        <p class="rincian-title">Ringkasan Status Kegiatan:</p>

        <table class="detail-table">
            <thead>
                <tr>
                    <th>Kegiatan</th>
                    <th width="15%">Total Tagihan</th>
                    <th width="15%">Total Dibayar</th>
                    <th width="15%">Sisa</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailKegiatan as $detail)
                @if(!empty($detail['kegiatan']->nama_kegiatan))
                <tr>
                    <td>{{ $detail['kegiatan']->nama_kegiatan }}</td>
                    <td class="text-right">Rp {{ number_format($detail['kegiatan']->nominal, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($detail['total_dibayar'], 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($detail['partisipasi'] === 'tidak_ikut')
                            -
                        @else
                            Rp {{ number_format($detail['sisa_pembayaran'], 0, ',', '.') }}
                        @endif
                    </td>
                    <td class="text-center">
                        @if($detail['partisipasi'] === 'tidak_ikut')
                            <span class="status-tidak-ikut">TIDAK IKUT</span>
                        @elseif($detail['is_lunas'])
                            <span class="status-lunas">LUNAS</span>
                        @else
                            <span class="status-belum">BELUM LUNAS</span>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer clearfix">
        <div class="left">
            {{--  --}}
        </div>
        <div class="right">
            <p>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</p>
            <img src="data:image/png;base64,{{ $barcodeData }}" style="width: 100px; height: auto; display: block; margin-bottom: 5px;" alt="Barcode">
            <p>({{ Auth::check() ? Auth::user()->name : $item->petugas->name ?? 'Administrator' }})</p>
        </div>
    </div>

</body>

</html>