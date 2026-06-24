<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManageUser extends Model
{
    protected $table='manage_users';

    protected $fillable=[
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'status',
        'photo',
        'last_active'
    ];

    protected $hidden=[
        'password'
    ];

    protected $casts=[
        'last_active'=>'datetime'
    ];
}