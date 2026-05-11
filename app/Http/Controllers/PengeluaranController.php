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
            'pengeluaran' => $pengeluaran,
            'search' => $search,
        ]);
    }

    /**
     * Simpan pengeluaran baru
     * Logic: Input nama barang manual, cek apakah barang sudah ada atau belum
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'jumlah_barang_dibeli' => 'required|integer|min:1',
            'harga_per_pcs' => 'required|numeric|min:0',
        ], [
            'nama_barang.required' => 'Nama barang harus diisi',
            'jumlah_barang_dibeli.required' => 'Jumlah harus diisi',
            'jumlah_barang_dibeli.min' => 'Jumlah minimal 1',
            'harga_per_pcs.required' => 'Harga per pcs harus diisi',
            'harga_per_pcs.numeric' => 'Harga harus berupa angka',
            'harga_per_pcs.min' => 'Harga tidak boleh minus',
        ]);

        // Hitung total pengeluaran
        $totalPengeluaran = $request->jumlah_barang_dibeli * $request->harga_per_pcs;

        DB::beginTransaction();
        try {
            // Cek apakah barang sudah ada di database (case insensitive)
            $barang = Barang::whereRaw('LOWER(nama_barang) = ?', [strtolower(trim($request->nama_barang))])->first();

            if ($barang) {
                // BARANG SUDAH ADA - Update stok dan harga
                $barang->stok_barang += $request->jumlah_barang_dibeli;
                $barang->harga_barang = $request->harga_per_pcs; // Update harga terbaru
                $barang->save();

                $idBarang = $barang->id_barang;
                $statusBarang = 'existing';
            } else {
                // BARANG BARU - Insert ke tabel barang
                $barangBaru = Barang::create([
                    'nama_barang' => trim($request->nama_barang),
                    'stok_barang' => $request->jumlah_barang_dibeli,
                    'harga_barang' => $request->harga_per_pcs,
                ]);

                $idBarang = $barangBaru->id_barang;
                $statusBarang = 'new';
            }

            // Simpan data pengeluaran
            Pengeluaran::create([
                'id_barang' => $idBarang,
                'jumlah_barang_dibeli' => $request->jumlah_barang_dibeli,
                'total_pengeluaran' => $totalPengeluaran,
            ]);

            DB::commit();

            // Pesan sukses sesuai status
            if ($statusBarang == 'new') {
                return redirect()->route('pengeluaran.index')->with('success', 'Barang baru berhasil ditambahkan! Stok: ' . $request->jumlah_barang_dibeli);
            } else {
                return redirect()->route('pengeluaran.index')->with('success', 'Stok barang berhasil ditambah! Total stok sekarang: ' . $barang->stok_barang);
            }
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
            'nama_barang' => 'required|string|max:255',
            'jumlah_barang_dibeli' => 'required|integer|min:1',
            'harga_per_pcs' => 'required|numeric|min:0',
        ], [
            'nama_barang.required' => 'Nama barang harus diisi',
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
        $barangLama = $pengeluaran->barang;

        DB::beginTransaction();
        try {
            // Cek apakah nama barang diubah atau sama
            $barangBaru = Barang::whereRaw('LOWER(nama_barang) = ?', [strtolower(trim($request->nama_barang))])->first();

            // Jika nama barang SAMA dengan yang lama
            if ($barangBaru && $barangBaru->id_barang == $barangLama->id_barang) {
                // Update stok dengan selisih
                $selisih = $request->jumlah_barang_dibeli - $pengeluaran->jumlah_barang_dibeli;
                $barangBaru->stok_barang += $selisih;
                $barangBaru->harga_barang = $request->harga_per_pcs;
                $barangBaru->save();

                $idBarangBaru = $barangBaru->id_barang;
            } 
            // Jika nama barang BERBEDA
            else {
                // Kembalikan stok barang lama
                $barangLama->stok_barang -= $pengeluaran->jumlah_barang_dibeli;
                $barangLama->save();

                // Cek apakah barang baru sudah ada atau belum
                if ($barangBaru) {
                    // Barang sudah ada - tambah stok
                    $barangBaru->stok_barang += $request->jumlah_barang_dibeli;
                    $barangBaru->harga_barang = $request->harga_per_pcs;
                    $barangBaru->save();
                    $idBarangBaru = $barangBaru->id_barang;
                } else {
                    // Barang belum ada - insert baru
                    $barangBaru = Barang::create([
                        'nama_barang' => trim($request->nama_barang),
                        'stok_barang' => $request->jumlah_barang_dibeli,
                        'harga_barang' => $request->harga_per_pcs,
                    ]);
                    $idBarangBaru = $barangBaru->id_barang;
                }
            }

            // Update data pengeluaran
            $pengeluaran->update([
                'id_barang' => $idBarangBaru,
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