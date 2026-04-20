<?php

namespace App\Http\Controllers;

use App\Models\Pemasukan;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemasukanController extends Controller
{
    /**
     * Tampilkan halaman pemasukan
     */
    public function index(Request $request)
    {
        // Ambil semua barang untuk dropdown
        $barangList = Barang::orderBy('nama_barang', 'asc')->get();

        // Ambil keyword search (kalau ada)
        $search = $request->input('search');

        // Query pemasukan dengan relasi barang dan search
        $pemasukan = Pemasukan::with('barang')
                        ->when($search, function($query) use ($search) {
                            return $query->whereHas('barang', function($q) use ($search) {
                                $q->where('nama_barang', 'LIKE', '%' . $search . '%');
                            });
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('pemasukan.index', [
            'barangList' => $barangList,
            'pemasukan' => $pemasukan,
            'search' => $search,
        ]);
    }

    /**
     * Simpan pemasukan baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_barang_dijual' => 'required|integer|min:1',
        ], [
            'id_barang.required' => 'Pilih barang terlebih dahulu',
            'id_barang.exists' => 'Barang tidak ditemukan',
            'jumlah_barang_dijual.required' => 'Jumlah harus diisi',
            'jumlah_barang_dijual.min' => 'Jumlah minimal 1',
        ]);

        // Ambil harga barang dari tabel barang
        $barang = Barang::findOrFail($request->id_barang);
        
        // Cek stok mencukupi
        if ($barang->stok_barang < $request->jumlah_barang_dijual) {
            return redirect()->back()->withErrors(['error' => 'Stok barang tidak mencukupi! Stok tersedia: ' . $barang->stok_barang])->withInput();
        }
        
        $totalPemasukan = $request->jumlah_barang_dijual * $barang->harga_barang;

        DB::beginTransaction();
        try {
            // Simpan data pemasukan
            Pemasukan::create([
                'id_barang' => $request->id_barang,
                'jumlah_barang_dijual' => $request->jumlah_barang_dijual,
                'total_pemasukan' => $totalPemasukan,
            ]);

            // Update stok barang (KURANG stok)
            $barang->stok_barang -= $request->jumlah_barang_dijual;
            $barang->save();

            DB::commit();
            return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil disimpan dan stok barang berkurang!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update pemasukan
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_barang_dijual' => 'required|integer|min:1',
        ], [
            'id_barang.required' => 'Pilih barang terlebih dahulu',
            'id_barang.exists' => 'Barang tidak ditemukan',
            'jumlah_barang_dijual.required' => 'Jumlah harus diisi',
            'jumlah_barang_dijual.min' => 'Jumlah minimal 1',
        ]);

        // Cari pemasukan yang akan diupdate
        $pemasukan = Pemasukan::findOrFail($id);
        
        // Ambil harga barang dari tabel barang
        $barang = Barang::findOrFail($request->id_barang);
        $totalPemasukan = $request->jumlah_barang_dijual * $barang->harga_barang;

        DB::beginTransaction();
        try {
            // Jika barang sama, hitung selisihnya
            if ($pemasukan->id_barang == $request->id_barang) {
                // Hitung selisih
                $selisih = $request->jumlah_barang_dijual - $pemasukan->jumlah_barang_dijual;
                
                // Cek stok mencukupi untuk selisih positif
                if ($selisih > 0 && $barang->stok_barang < $selisih) {
                    DB::rollBack();
                    return redirect()->back()->withErrors(['error' => 'Stok barang tidak mencukupi! Stok tersedia: ' . $barang->stok_barang])->withInput();
                }
                
                // Update stok sesuai selisih
                // Jika selisih positif (nambah jumlah jual) → stok kurang
                // Jika selisih negatif (kurang jumlah jual) → stok nambah
                $barang->stok_barang -= $selisih;
                $barang->save();
            } else {
                // Jika barang beda, kembalikan stok lama dan kurangi stok baru
                $barangLama = Barang::find($pemasukan->id_barang);
                $barangLama->stok_barang += $pemasukan->jumlah_barang_dijual;
                $barangLama->save();
                
                // Cek stok baru mencukupi
                if ($barang->stok_barang < $request->jumlah_barang_dijual) {
                    DB::rollBack();
                    return redirect()->back()->withErrors(['error' => 'Stok barang tidak mencukupi! Stok tersedia: ' . $barang->stok_barang])->withInput();
                }
                
                $barang->stok_barang -= $request->jumlah_barang_dijual;
                $barang->save();
            }

            // Update data pemasukan
            $pemasukan->update([
                'id_barang' => $request->id_barang,
                'jumlah_barang_dijual' => $request->jumlah_barang_dijual,
                'total_pemasukan' => $totalPemasukan,
            ]);

            DB::commit();
            return redirect()->route('pemasukan.index')->with('success', 'Data pemasukan berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}