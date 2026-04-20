<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanHarianController extends Controller
{
    /**
     * Tampilkan laporan harian
     */
    public function index(Request $request)
    {
        // Ambil input tanggal dari user
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalAkhir = $request->input('tanggal_akhir');

        // Default: bulan ini kalau tidak ada input
        if (!$tanggalMulai || !$tanggalAkhir) {
            $tanggalMulai = Carbon::now()->startOfMonth()->format('Y-m-d');
            $tanggalAkhir = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        // Query pemasukan per hari
        $pemasukan = Pemasukan::selectRaw('DATE(created_at) as tanggal, SUM(total_pemasukan) as total')
                    ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalAkhir])
                    ->groupBy('tanggal')
                    ->get()
                    ->keyBy('tanggal');

        // Query pengeluaran per hari
        $pengeluaran = Pengeluaran::selectRaw('DATE(created_at) as tanggal, SUM(total_pengeluaran) as total')
                    ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalAkhir])
                    ->groupBy('tanggal')
                    ->get()
                    ->keyBy('tanggal');

        // Gabungkan semua tanggal yang ada di pemasukan dan pengeluaran
        $semuaTanggal = collect($pemasukan->keys())
                        ->merge($pengeluaran->keys())
                        ->unique()
                        ->sort()
                        ->values();

        // Hitung laba bersih per hari
        $laporan = $semuaTanggal->map(function($tanggal) use ($pemasukan, $pengeluaran) {
            $totalPemasukan = $pemasukan->get($tanggal)->total ?? 0;
            $totalPengeluaran = $pengeluaran->get($tanggal)->total ?? 0;
            $labaBersih = $totalPemasukan - $totalPengeluaran;

            return [
                'tanggal' => $tanggal,
                'tanggal_format' => Carbon::parse($tanggal)->format('d-m-Y'),
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'laba_bersih' => $labaBersih,
            ];
        });

        // Ambil detail transaksi per hari untuk modal
        $detailPemasukan = Pemasukan::with('barang')
                            ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalAkhir])
                            ->get()
                            ->groupBy(function($item) {
                                return $item->created_at->format('Y-m-d');
                            });

        $detailPengeluaran = Pengeluaran::with('barang')
                            ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalAkhir])
                            ->get()
                            ->groupBy(function($item) {
                                return $item->created_at->format('Y-m-d');
                            });

        // Format detail transaksi
        $detailTransaksi = [];
        foreach ($semuaTanggal as $tanggal) {
            $detailTransaksi[$tanggal] = [
                'pemasukan' => $detailPemasukan->get($tanggal, collect())->map(function($item) {
                    return [
                        'nama_barang' => $item->barang->nama_barang ?? '-',
                        'jumlah' => $item->jumlah_barang_dijual,
                        'total' => $item->total_pemasukan,
                    ];
                })->toArray(),
                'pengeluaran' => $detailPengeluaran->get($tanggal, collect())->map(function($item) {
                    return [
                        'nama_barang' => $item->barang->nama_barang ?? '-',
                        'jumlah' => $item->jumlah_barang_dibeli,
                        'total' => $item->total_pengeluaran,
                    ];
                })->toArray(),
            ];
        }

        return view('laporan.harian', [
            'laporan' => $laporan,
            'tanggalMulai' => $tanggalMulai,
            'tanggalAkhir' => $tanggalAkhir,
            'detailTransaksi' => $detailTransaksi,
        ]);
    }
}