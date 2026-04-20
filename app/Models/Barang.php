<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $primaryKey = 'id_barang';
    public $timestamps = true;

    protected $fillable = [
        'nama_barang',
        'stok_barang',
        'harga_barang',
    ];

    protected $casts = [
        'stok_barang' => 'integer',
        'harga_barang' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi: Barang memiliki banyak Pemasukan
     */
    public function pemasukan()
    {
        return $this->hasMany(Pemasukan::class, 'id_barang', 'id_barang');
    }

    /**
     * Relasi: Barang memiliki banyak Pengeluaran
     */
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_barang', 'id_barang');
    }
}