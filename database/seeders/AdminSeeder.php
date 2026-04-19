<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua data admin lama
        DB::table('admin')->truncate();

        // Insert admin baru
        DB::table('admin')->insert([
            'username' => 'admin',
            'password' => 'admin123', 
            'created_at' => now(),
        ]);
        echo "✅ Table admin berhasil di-reset!\n";//biar keren aja sih xixixi
        echo "   Username: admin\n";
        echo "   Password: admin123\n";
    }
}