<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    /**
     * Tampilkan halaman data barang
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Ambil harga per pcs dari pembelian TERAKHIR (terbaru) untuk setiap barang
        $modalPerBarang = Pengeluaran::select('id_barang', 'total_pengeluaran', 'jumlah_barang_dibeli', 'created_at')
                          ->whereIn('id_pengeluaran', function($query) {
                              $query->select(DB::raw('MAX(id_pengeluaran)'))
                                    ->from('pengeluaran')
                                    ->groupBy('id_barang');
                          })
                          ->get()
                          ->mapWithKeys(function($item) {
                              // Hitung harga per pcs dari pembelian terakhir
                              $hargaSatuan = $item->jumlah_barang_dibeli > 0
                                  ? $item->total_pengeluaran / $item->jumlah_barang_dibeli
                                  : 0;
                              return [$item->id_barang => $hargaSatuan];
                          });

        $barang = Barang::when($search, function($query) use ($search) {
                        return $query->where('nama_barang', 'LIKE', '%' . $search . '%');
                    })
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($item) use ($modalPerBarang) {
                        $item->modal_per_satuan = $modalPerBarang->get($item->id_barang, 0);
                        return $item;
                    });

        return view('barang.index', [
            'barang' => $barang,
            'search' => $search,
        ]);
    }

    /**
     * Simpan barang baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'  => 'required|string|max:255',
            'stok_barang'  => 'required|integer|min:0',
            'harga_barang' => 'required|numeric|min:0',
        ], [
            'nama_barang.required'  => 'Nama barang harus diisi',
            'stok_barang.required'  => 'Stok barang harus diisi',
            'stok_barang.integer'   => 'Stok harus berupa angka',
            'stok_barang.min'       => 'Stok tidak boleh minus',
            'harga_barang.required' => 'Harga barang harus diisi',
            'harga_barang.numeric'  => 'Harga harus berupa angka',
            'harga_barang.min'      => 'Harga tidak boleh minus',
        ]);

        Barang::create([
            'nama_barang'  => $request->nama_barang,
            'stok_barang'  => $request->stok_barang,
            'harga_barang' => $request->harga_barang,
        ]);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil disimpan!');
    }

    /**
     * Hapus barang yang dipilih (multiple delete)
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'selected_barang'   => 'required|array|min:1',
            'selected_barang.*' => 'exists:barang,id_barang',
        ], [
            'selected_barang.required' => 'Pilih minimal 1 barang untuk dihapus',
            'selected_barang.min'      => 'Pilih minimal 1 barang untuk dihapus',
        ]);

        Barang::whereIn('id_barang', $request->selected_barang)->delete();

        $jumlahDihapus = count($request->selected_barang);

        return redirect()->route('barang.index')->with('success', $jumlahDihapus . ' barang berhasil dihapus!');
    }

    /**
     * Update barang
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_barang'  => 'required|string|max:255',
            'stok_barang'  => 'required|integer|min:0',
            'harga_barang' => 'required|numeric|min:0',
        ], [
            'nama_barang.required'  => 'Nama barang harus diisi',
            'stok_barang.required'  => 'Stok barang harus diisi',
            'stok_barang.integer'   => 'Stok harus berupa angka',
            'stok_barang.min'       => 'Stok tidak boleh minus',
            'harga_barang.required' => 'Harga barang harus diisi',
            'harga_barang.numeric'  => 'Harga harus berupa angka',
            'harga_barang.min'      => 'Harga tidak boleh minus',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update([
            'nama_barang'  => $request->nama_barang,
            'stok_barang'  => $request->stok_barang,
            'harga_barang' => $request->harga_barang,
        ]);

        return redirect()->route('barang.index')->with('success', 'Data barang berhasil diupdate!');
    }
}