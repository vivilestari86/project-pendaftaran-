<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'profesi',
        'email',
        'email_verified_at',
        'password',
        'role',
        'status',
        'last_active_at',
        'phone_number',
        'profile_photo',
        'terms_agreed',
        'documents_submitted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_active_at' => 'datetime',
        'password' => 'hashed',
        'terms_agreed' => 'boolean',
        'documents_submitted_at' => 'datetime',
    ];

    // Check apakah user adalah admin
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Check apakah user adalah user pengguna
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        return $this->profile_photo ? asset($this->profile_photo) : null;
    }

    public function documents(): HasMany
    {
        return $this->hasMany(UserDocument::class);
    }
}
