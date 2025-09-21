<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Prescription extends Model
{
    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'prescription_number',
        'user_id',
        'doctor_name',
        'patient_name',
        'notes',
        'prescription_image',
        'status',
        'confirmed_by',
        'confirmation_notes',
        'confirmed_at',
        'order_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            if (empty($prescription->prescription_number)) {
                $prescription->prescription_number = 'RX-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the user who uploaded this prescription
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the apoteker who confirmed this prescription
     */
    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by', 'user_id');
    }

    /**
     * Get the order associated with the prescription
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the image URL attribute
     */
    public function getImageUrlAttribute()
    {
        if ($this->prescription_image) {
            return asset('storage/' . $this->prescription_image);
        }
        return asset('images/no-prescription.png');
    }

    /**
     * Get status text in Indonesian
     */
    public function getStatusTextAttribute()
    {
        $statusTexts = [
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'rejected' => 'Ditolak',
            'processed' => 'Diproses'
        ];

        return $statusTexts[$this->status] ?? 'Tidak Diketahui';
    }

    /**
     * Get status badge color class
     */
    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'pending' => 'badge-warning',
            'confirmed' => 'badge-success',
            'rejected' => 'badge-error',
            'processed' => 'badge-info'
        ];

        return $colors[$this->status] ?? 'badge-ghost';
    }

    /**
     * Scope for pending prescriptions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed prescriptions.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
