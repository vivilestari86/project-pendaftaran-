<?php

namespace Database\Seeders;

use App\Models\Pendaftar;
use Illuminate\Database\Seeder;

class PendaftarSeeder extends Seeder
{
    public function run(): void
    {
        if (Pendaftar::count() > 0) {
            return;
        }

        Pendaftar::factory(60)->create();

        Pendaftar::factory(8)
            ->state(fn () => ['wilayah' => 'Jawa Barat', 'status_kelengkapan' => 'lengkap'])
            ->create();

        Pendaftar::factory(6)
            ->state(fn () => ['wilayah' => 'Jawa Timur', 'status_kelengkapan' => 'belum_lengkap'])
            ->create();
    }
}
