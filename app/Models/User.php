<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role_id',
        'status',
        'date_of_birth',
        'gender',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    // Relasi dengan Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    // Helper methods untuk check role
    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    public function isApoteker()
    {
        return $this->role->name === 'apoteker';
    }

    public function isKurir()
    {
        return $this->role->name === 'kurir';
    }

    public function isPelanggan()
    {
        return $this->role->name === 'pelanggan';
    }

    // Relasi lainnya
    // public function addresses()
    // {
    //     return $this->hasMany(UserAddress::class, 'user_id', 'user_id');
    // }

    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class, 'user_id', 'user_id');
    // }

    // public function orders()
    // {
    //     return $this->hasMany(Order::class, 'user_id', 'user_id');
    // }

    // public function cart()
    // {
    //     return $this->hasMany(Cart::class, 'user_id', 'user_id');
    // }
}
