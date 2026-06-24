<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftar extends Model
{
    use HasFactory;

    protected $table = 'pendaftars';

    protected $fillable = [
        'nama',
        'id_pendaftar',
        'profesi',
        'foto',
        'wilayah',
        'status_kelengkapan',
        'email',
        'no_hp',
        'alamat',
        'catatan',
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeLengkap(Builder $query): Builder
    {
        return $query->where('status_kelengkapan', 'lengkap');
    }

    public function scopeBelumLengkap(Builder $query): Builder
    {
        return $query->where('status_kelengkapan', 'belum_lengkap');
    }

    public function scopeHariIni(Builder $query): Builder
    {
        return $query->whereDate('tanggal_daftar', now()->toDateString());
    }

    public function getWaktuRelatifAttribute(): string
    {
        return $this->tanggal_daftar?->diffForHumans() ?? '-';
    }

    public function getBadgeColorAttribute(): string
    {
        return $this->status_kelengkapan === 'lengkap' ? 'success' : 'warning';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status_kelengkapan === 'lengkap' ? 'LENGKAP' : 'BELUM LENGKAP';
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->nama)) ?: [];
        $initials = collect($words)
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');

        return $initials ?: 'P';
    }
}
