<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserAddress;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'role_id',
        'status',
        'date_of_birth',
        'gender',
        'avatar',
        'verification_token',
        'verification_token_expires_at',
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
            'verification_token_expires_at' => 'datetime',
        ];
    }

    // Relasi dengan Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'role_id');
    }

    /**
     * Get user avatar URL with fallback to role-based default
     * 
     * @return string
     */
    public function getAvatarUrl()
    {
        // Check if user has custom avatar
        if ($this->avatar && file_exists(storage_path('app/public/' . $this->avatar))) {
            return asset('storage/' . $this->avatar);
        }
        
        // Return role-based default avatar
        return $this->getDefaultAvatarByRole();
    }

    /**
     * Get default avatar based on user role
     * 
     * @return string
     */
    private function getDefaultAvatarByRole()
    {
        $roleAvatars = [
            'admin' => asset('src/img/avatars/default-admin.svg'),
            'apoteker' => asset('src/img/avatars/default-apoteker.svg'),
            'kurir' => asset('src/img/avatars/default-kurir.svg'),
            'pelanggan' => asset('src/img/avatars/default-pelanggan.svg'),
        ];

        // Get user's primary role name
        $userRole = $this->role ? $this->role->name : 'pelanggan';
        
        return $roleAvatars[$userRole] ?? $roleAvatars['pelanggan'];
    }

    // Helper methods untuk check role
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

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
    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'user_id', 'user_id');
    }

    // public function notifications()
    // {
    //     return $this->hasMany(Notification::class, 'user_id', 'user_id');
    // }

    // public function orders()
    // {
    //     return $this->hasMany(Order::class, 'user_id', 'user_id');
    // }

    public function cart()
    {
        return $this->hasOne(Cart::class, 'user_id', 'user_id');
    }

    /**
     * Get or create user's cart
     */
    public function getOrCreateCart()
    {
        return Cart::getOrCreateForUser($this->user_id);
    }

    /**
     * Get cart items count
     */
    public function getCartItemsCountAttribute()
    {
        $cart = $this->cart;
        return $cart ? $cart->total_items : 0;
    }
}
