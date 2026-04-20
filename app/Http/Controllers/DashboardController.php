<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan laporan bulan ini
     */
    public function index()
    {
        // Ambil bulan dan tahun sekarang
        $bulanIni = Carbon::now()->month;
        $tahunIni = Carbon::now()->year;
        $namaBulan = Carbon::now()->locale('id')->isoFormat('MMMM YYYY');

        // Hitung total pemasukan bulan ini
        $totalPemasukan = Pemasukan::whereRaw('MONTH(created_at) = ?', [$bulanIni])
                                    ->whereRaw('YEAR(created_at) = ?', [$tahunIni])
                                    ->sum('total_pemasukan');

        // Hitung total pengeluaran bulan ini
        $totalPengeluaran = Pengeluaran::whereRaw('MONTH(created_at) = ?', [$bulanIni])
                                       ->whereRaw('YEAR(created_at) = ?', [$tahunIni])
                                       ->sum('total_pengeluaran');

        // Pastikan nilai adalah angka, bukan null
        $totalPemasukan   = $totalPemasukan ?? 0;
        $totalPengeluaran = $totalPengeluaran ?? 0;

        // Hitung laba bersih
        $labaBersih = $totalPemasukan - $totalPengeluaran;

        // Kirim data ke view
        return view('dashboard', [
            'namaBulan' => $namaBulan,
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'labaBersih' => $labaBersih,
        ]);
    }
}