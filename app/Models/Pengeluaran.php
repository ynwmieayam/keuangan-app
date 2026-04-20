<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    protected $primaryKey = 'id_pengeluaran';
    public $timestamps = true;

    protected $fillable = [
        'id_barang',
        'jumlah_barang_dibeli',
        'total_pengeluaran',
    ];

    protected $casts = [
        'id_barang' => 'integer',
        'jumlah_barang_dibeli' => 'integer',
        'total_pengeluaran' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: Pengeluaran milik satu Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}