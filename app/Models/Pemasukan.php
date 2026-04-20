<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan';
    protected $primaryKey = 'id_pemasukan';
    public $timestamps = true;

    protected $fillable = [
        'id_barang',
        'jumlah_barang_dijual',
        'total_pemasukan',
    ];

    protected $casts = [
        'id_barang' => 'integer',
        'jumlah_barang_dijual' => 'integer',
        'total_pemasukan' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi: Pemasukan milik satu Barang
     */
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}