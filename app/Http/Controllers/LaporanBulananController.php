<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanBulananController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', Carbon::now()->year);

        // ── Ringkasan bulanan ─────────────────────────────────────────────
        $pemasukanBulanan = Pemasukan::selectRaw('MONTH(created_at) as bulan, SUM(total_pemasukan) as total')
                    ->whereYear('created_at', $tahun)
                    ->groupBy('bulan')
                    ->get()->keyBy('bulan');

        $pengeluaranBulanan = Pengeluaran::selectRaw('MONTH(created_at) as bulan, SUM(total_pengeluaran) as total')
                    ->whereYear('created_at', $tahun)
                    ->groupBy('bulan')
                    ->get()->keyBy('bulan');

        // Hanya bulan yang ada datanya
        $bulanAda = collect($pemasukanBulanan->keys())
                    ->merge($pengeluaranBulanan->keys())
                    ->unique()->sort()->values();

        $laporan = collect();
        $labaKumulatif = 0; // Reset otomatis per tahun karena query whereYear()
        
        foreach ($bulanAda as $bulan) {
            $totalPemasukan   = $pemasukanBulanan->get($bulan)->total   ?? 0;
            $totalPengeluaran = $pengeluaranBulanan->get($bulan)->total ?? 0;
            $labaKotor        = $totalPemasukan - $totalPengeluaran;  // Laba bulan ini saja
            $labaKumulatif   += $labaKotor;                            // Kumulatif dalam 1 tahun ini
            
            $namaBulan = Carbon::create($tahun, $bulan, 1)->locale('id')->isoFormat('MMMM');

            $laporan->push([
                'bulan'             => $bulan,
                'nama_bulan'        => $namaBulan,
                'bulan_tahun'       => "$namaBulan $tahun",
                'total_pemasukan'   => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'laba_kotor'        => $labaKotor,           // Laba bulan ini saja
                'laba_bersih'       => $labaKumulatif,       // Kumulatif Jan-Des tahun ini
            ]);
        }

        // ── Detail harian ─────────────────────────────────────────────────
        $pemasukanHarian = Pemasukan::selectRaw('DATE(created_at) as tanggal, SUM(total_pemasukan) as total')
                    ->whereYear('created_at', $tahun)
                    ->groupBy('tanggal')->get()->keyBy('tanggal');

        $pengeluaranHarian = Pengeluaran::selectRaw('DATE(created_at) as tanggal, SUM(total_pengeluaran) as total')
                    ->whereYear('created_at', $tahun)
                    ->groupBy('tanggal')->get()->keyBy('tanggal');

        $detailPemasukanHarian = Pemasukan::with('barang')
                    ->whereYear('created_at', $tahun)->get()
                    ->groupBy(fn($item) => $item->created_at->format('Y-m-d'));

        $detailPengeluaranHarian = Pengeluaran::with('barang')
                    ->whereYear('created_at', $tahun)->get()
                    ->groupBy(fn($item) => $item->created_at->format('Y-m-d'));

        $semuaTanggal = collect($pemasukanHarian->keys())
                        ->merge($pengeluaranHarian->keys())
                        ->unique()->sort()->values();

        // Dikelompokkan per bulan (key = int bulan 1-12)
        $detailHarian = [];
        foreach ($semuaTanggal as $tanggal) {
            $bulanTanggal     = (int) Carbon::parse($tanggal)->format('n');
            $totalPemasukan   = $pemasukanHarian->get($tanggal)->total   ?? 0;
            $totalPengeluaran = $pengeluaranHarian->get($tanggal)->total ?? 0;

            $detailHarian[$bulanTanggal][] = [
                'tanggal'           => $tanggal,
                'tanggal_format'    => Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY'),
                'total_pemasukan'   => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'laba_bersih'       => $totalPemasukan - $totalPengeluaran,
                'pemasukan'         => ($detailPemasukanHarian->get($tanggal) ?? collect())->map(fn($item) => [
                    'nama_barang' => $item->barang->nama_barang ?? '-',
                    'jumlah'      => $item->jumlah_barang_dijual,
                    'total'       => $item->total_pemasukan,
                ])->values()->toArray(),
                'pengeluaran'       => ($detailPengeluaranHarian->get($tanggal) ?? collect())->map(fn($item) => [
                    'nama_barang' => $item->barang->nama_barang ?? '-',
                    'jumlah'      => $item->jumlah_barang_dibeli,
                    'total'       => $item->total_pengeluaran,
                ])->values()->toArray(),
            ];
        }

        $tahunPertama  = Pemasukan::min(DB::raw('YEAR(created_at)')) ?? Carbon::now()->year;
        $tahunSekarang = Carbon::now()->year;
        $tahunList     = range($tahunSekarang, $tahunPertama);

        return view('laporan.bulanan', [
            'laporan'      => $laporan,
            'detailHarian' => $detailHarian,
            'tahun'        => $tahun,
            'tahunList'    => $tahunList,
        ]);
    }
}