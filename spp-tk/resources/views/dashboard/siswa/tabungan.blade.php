@extends('layouts.dashboard-siswa')

@section('breadcrumb')
    <li class="breadcrumb-item">Dashboard</li>
    <li class="breadcrumb-item active">Histori Tabungan</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div style="background: #fff; border-radius: 15px; box-shadow: 0 0 20px rgba(0,0,0,0.05); margin-bottom: 30px;">
                <!-- Header Section with Gradient Background -->
                <div
                    style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); padding: 25px; border-radius: 15px 15px 0 0; color: white;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h4 style="margin: 0; font-size: 20px; font-weight: 600;">
                                <i class="fas fa-wallet mr-2"></i>Histori Tabungan Saya
                            </h4>
                            <p style="margin: 5px 0 0 0; opacity: 0.8; font-size: 14px;">
                                Ringkasan transaksi tabungan Anda
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <div style="background: rgba(255,255,255,0.1); padding: 12px 20px; border-radius: 10px;">
                                <p style="margin: 0; font-size: 14px; opacity: 0.9;">Total Saldo</p>
                                <h4 style="margin: 5px 0 0 0; font-size: 24px;">
                                    <i class="fas fa-coins mr-2"></i>Rp {{ number_format($saldo, 0, ',', '.') }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <a href="{{ route('siswa.tabungan.cetak') }}"
                            style="background: rgba(255,255,255,0.1); padding: 10px 20px; border-radius: 8px; color: white; text-decoration: none; display: inline-flex; align-items: center; transition: all 0.3s ease;"
                            target="_blank" onmouseover="this.style.background='rgba(255,255,255,0.2)'"
                            onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Cetak Rekap Tabungan
                        </a>
                    </div>
                </div>

                <div style="padding: 25px;">
                    <div class="table-responsive">
                        <table class="table" style="margin: 0;">
                            <thead>
                                <tr style="background: #f8f9fc; border-radius: 8px;">
                                    <th
                                        style="padding: 15px; border-top: none; color: #4e73df; font-weight: 600; text-align: center;">No
                                    </th>
                                    <th
                                        style="padding: 15px; border-top: none; color: #4e73df; font-weight: 600; text-align: center;">Tanggal
                                    </th>
                                    <th
                                        style="padding: 15px; border-top: none; color: #4e73df; font-weight: 600; text-align: center;">Jenis
                                    </th>
                                    <th
                                        style="padding: 15px; border-top: none; color: #4e73df; font-weight: 600; text-align: center;">Nominal
                                    </th>
                                    <th
                                        style="padding: 15px; border-top: none; color: #4e73df; font-weight: 600; text-align: center;">Saldo
                                    </th>
                                    <th
                                        style="padding: 15px; border-top: none; color: #4e73df; font-weight: 600; text-align: center;">Petugas
                                    </th>
                                    <th
                                        style="padding: 15px; border-top: none; color: #4e73df; font-weight: 600; text-align: center;">Keterangan
                                    </th>
                                    <th
                                        style="padding: 15px; border-top: none; color: #4e73df; font-weight: 600; text-align: center;">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tabunganHistori as $transaksi)
                                    <tr style="border-bottom: 1px solid #f0f0f0; transition: all 0.3s ease;"
                                        onmouseover="this.style.backgroundColor='#f8f9fc'"
                                        onmouseout="this.style.backgroundColor='transparent'">
                                        <td
                                            style="padding: 15px; vertical-align: middle; text-align: center; font-weight: 600;">
                                            {{ $tabunganHistori->firstItem() + $loop->index }}
                                        </td>
                                        <td style="padding: 15px; vertical-align: middle;">
                                            <div style="font-weight: 500;">{{ $transaksi->created_at->format('d M Y') }}
                                            </div>
                                        </td>
                                        <td style="padding: 15px; vertical-align: middle;">
                                            @if ($transaksi->debit > 0)
                                                <div
                                                    style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); color: white; border-radius: 20px; font-size: 13px;">
                                                    <i class="fas fa-arrow-up mr-1"></i>Setor
                                                </div>
                                            @else
                                                <div
                                                    style="display: inline-block; padding: 6px 12px; background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; border-radius: 20px; font-size: 13px;">
                                                    <i class="fas fa-arrow-down mr-1"></i>Tarik
                                                </div>
                                            @endif
                                        </td>
                                        <td style="padding: 15px; vertical-align: middle; font-weight: 500;">
                                            @if ($transaksi->debit > 0)
                                                <span style="color: #2ecc71;">
                                                    + Rp {{ number_format($transaksi->debit, 0, ',', '.') }}
                                                </span>
                                            @else
                                                <span style="color: #e74c3c;">
                                                    - Rp {{ number_format($transaksi->kredit, 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td style="padding: 15px; vertical-align: middle; font-weight: 600;">
                                            Rp {{ number_format($transaksi->saldo, 0, ',', '.') }}
                                        </td>
                                        <td style="padding: 15px; vertical-align: middle;">
                                            <div
                                                style="display: inline-flex; align-items: center; background: #f8f9fc; padding: 6px 12px; border-radius: 20px;">
                                                <i class="fas fa-user-circle mr-2" style="color: #4e73df;"></i>
                                                {{ $transaksi->petugas->name ?? 'System' }}
                                            </div>
                                        </td>
                                        <td style="padding: 15px; vertical-align: middle; color: #6c757d;">
                                            {{ $transaksi->keterangan }}
                                        </td>
                                        <td style="padding: 15px; vertical-align: middle; text-align: center;">
                                            <a href="{{ route('siswa.tabungan.single.cetak', $transaksi->id) }}"
                                                style="background: #28a745; color: white; padding: 8px 16px; border-radius: 5px; text-decoration: none; display: inline-flex; align-items: center; transition: background 0.3s; font-size: 13px;"
                                                onmouseover="this.style.background='#218838'"
                                                onmouseout="this.style.background='#28a745'" target="_blank">
                                                <i class="fas fa-print mr-2"></i>
                                                Cetak
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8">
                                            <div style="text-align: center; padding: 40px 20px;">
                                                <div
                                                    style="background: #f8f9fc; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                                    <i class="fas fa-inbox" style="font-size: 30px; color: #b2bec3;"></i>
                                                </div>
                                                <h5 style="color: #2d3436; margin-bottom: 10px;">Belum Ada Transaksi</h5>
                                                <p style="color: #636e72; margin: 0;">Belum ada catatan transaksi tabungan
                                                    saat ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 25px;">
                        {{ $tabunganHistori->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
