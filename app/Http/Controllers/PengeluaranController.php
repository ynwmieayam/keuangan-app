<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengeluaranController extends Controller
{
    /**
     * Tampilkan halaman pengeluaran
     */
    public function index(Request $request)
    {
        // Ambil semua barang untuk dropdown
        $barangList = Barang::orderBy('nama_barang', 'asc')->get();

        // Ambil keyword search (kalau ada)
        $search = $request->input('search');

        // Query pengeluaran dengan relasi barang dan search
        $pengeluaran = Pengeluaran::with('barang')
                        ->when($search, function($query) use ($search) {
                            return $query->whereHas('barang', function($q) use ($search) {
                                $q->where('nama_barang', 'LIKE', '%' . $search . '%');
                            });
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('pengeluaran.index', [
            'barangList' => $barangList,
            'pengeluaran' => $pengeluaran,
            'search' => $search,
        ]);
    }

    /**
     * Simpan pengeluaran baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_barang_dibeli' => 'required|integer|min:1',
            'harga_per_pcs' => 'required|numeric|min:0',
        ], [
            'id_barang.required' => 'Pilih barang terlebih dahulu',
            'id_barang.exists' => 'Barang tidak ditemukan',
            'jumlah_barang_dibeli.required' => 'Jumlah harus diisi',
            'jumlah_barang_dibeli.min' => 'Jumlah minimal 1',
            'harga_per_pcs.required' => 'Harga per pcs harus diisi',
            'harga_per_pcs.numeric' => 'Harga harus berupa angka',
            'harga_per_pcs.min' => 'Harga tidak boleh minus',
        ]);

        // Hitung total pengeluaran
        $totalPengeluaran = $request->jumlah_barang_dibeli * $request->harga_per_pcs;

        // Ambil barang
        $barang = Barang::findOrFail($request->id_barang);

        DB::beginTransaction();
        try {
            // Simpan data pengeluaran
            Pengeluaran::create([
                'id_barang' => $request->id_barang,
                'jumlah_barang_dibeli' => $request->jumlah_barang_dibeli,
                'total_pengeluaran' => $totalPengeluaran,
            ]);

            // Update stok barang (TAMBAH stok karena beli barang)
            $barang->stok_barang += $request->jumlah_barang_dibeli;
            $barang->save();

            DB::commit();
            return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil disimpan dan stok barang bertambah!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update pengeluaran
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah_barang_dibeli' => 'required|integer|min:1',
            'harga_per_pcs' => 'required|numeric|min:0',
        ], [
            'id_barang.required' => 'Pilih barang terlebih dahulu',
            'id_barang.exists' => 'Barang tidak ditemukan',
            'jumlah_barang_dibeli.required' => 'Jumlah harus diisi',
            'jumlah_barang_dibeli.min' => 'Jumlah minimal 1',
            'harga_per_pcs.required' => 'Harga per pcs harus diisi',
            'harga_per_pcs.numeric' => 'Harga harus berupa angka',
            'harga_per_pcs.min' => 'Harga tidak boleh minus',
        ]);

        // Hitung total pengeluaran
        $totalPengeluaran = $request->jumlah_barang_dibeli * $request->harga_per_pcs;

        // Cari pengeluaran yang akan diupdate
        $pengeluaran = Pengeluaran::findOrFail($id);
        
        // Ambil barang
        $barangBaru = Barang::findOrFail($request->id_barang);

        DB::beginTransaction();
        try {
            // Jika barang SAMA, hitung selisih stok aja
            if ($pengeluaran->id_barang == $request->id_barang) {
                $selisih = $request->jumlah_barang_dibeli - $pengeluaran->jumlah_barang_dibeli;
                $barangBaru->stok_barang += $selisih;
                $barangBaru->save();
            } 
            // Jika barang BEDA
            else {
                // Kembalikan stok barang lama
                $barangLama = Barang::find($pengeluaran->id_barang);
                $barangLama->stok_barang -= $pengeluaran->jumlah_barang_dibeli;
                $barangLama->save();
                
                // Tambahkan ke stok barang baru
                $barangBaru->stok_barang += $request->jumlah_barang_dibeli;
                $barangBaru->save();
            }

            // Update data pengeluaran
            $pengeluaran->update([
                'id_barang' => $request->id_barang,
                'jumlah_barang_dibeli' => $request->jumlah_barang_dibeli,
                'total_pengeluaran' => $totalPengeluaran,
            ]);

            DB::commit();
            return redirect()->route('pengeluaran.index')->with('success', 'Data pengeluaran berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }
}