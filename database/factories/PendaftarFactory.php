<?php

namespace Database\Factories;

use App\Models\Pendaftar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pendaftar>
 */
class PendaftarFactory extends Factory
{
    protected $model = Pendaftar::class;

    public function definition(): array
    {
        $tanggalDaftar = fake()->dateTimeBetween('-45 days', 'now');

        return [
            'nama' => fake()->name(),
            'id_pendaftar' => 'PDF-' . fake()->unique()->numerify('######'),
            'profesi' => fake()->randomElement([
                'Pelajar',
                'Mahasiswa',
                'Karyawan Swasta',
                'Wiraswasta',
                'Guru',
                'Freelancer',
            ]),
            'foto' => null,
            'wilayah' => fake()->randomElement(['Jawa Barat', 'Jawa Timur', 'Jawa Tengah', 'Lainnya']),
            'status_kelengkapan' => fake()->randomElement(['lengkap', 'belum_lengkap']),
            'email' => fake()->optional(0.9)->safeEmail(),
            'no_hp' => fake()->optional(0.95)->numerify('08##########'),
            'alamat' => fake()->optional(0.85)->address(),
            'catatan' => fake()->optional(0.3)->sentence(),
            'tanggal_daftar' => $tanggalDaftar,
            'created_at' => $tanggalDaftar,
            'updated_at' => fake()->dateTimeBetween($tanggalDaftar, 'now'),
        ];
    }
}
