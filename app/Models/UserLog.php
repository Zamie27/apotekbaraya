<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserLog extends Model
{
    use HasFactory;

    protected $table = 'user_logs';
    protected $primaryKey = 'user_log_id';
    public $timestamps = false; // Using custom timestamp field

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'details',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'details' => 'array', // Cast JSON to array
    ];

    /**
     * Relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Create a new activity log entry
     * 
     * @param int $userId
     * @param string $action
     * @param string $description
     * @param array $details
     * @return UserLog
     */
    public static function createLog($userId, $action, $description = null, $details = [])
    {
        return self::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'details' => $details,
            'timestamp' => Carbon::now(),
        ]);
    }

    /**
     * Log delivery activity with courier and proof details
     * 
     * @param int $userId
     * @param string $action
     * @param array $deliveryDetails
     * @return UserLog
     */
    public static function logDeliveryActivity($userId, $action, $deliveryDetails = [])
    {
        $description = self::generateDeliveryDescription($action, $deliveryDetails);
        
        return self::createLog($userId, $action, $description, $deliveryDetails);
    }

    /**
     * Generate description for delivery activities
     * 
     * @param string $action
     * @param array $details
     * @return string
     */
    private static function generateDeliveryDescription($action, $details)
    {
        switch ($action) {
            case 'delivery_assigned':
                return "Pesanan #{$details['order_id']} ditugaskan ke kurir {$details['courier_name']}";
            case 'delivery_started':
                return "Kurir {$details['courier_name']} memulai pengiriman pesanan #{$details['order_id']}";
            case 'delivery_completed':
                return "Pesanan #{$details['order_id']} berhasil diantar oleh kurir {$details['courier_name']}";
            case 'delivery_proof_uploaded':
                return "Bukti pengiriman pesanan #{$details['order_id']} diunggah oleh kurir {$details['courier_name']}";
            default:
                return $details['description'] ?? 'Aktivitas pengiriman';
        }
    }

    /**
     * Get formatted timestamp for display
     */
    public function getFormattedTimestampAttribute()
    {
        return $this->timestamp->format('d/m/Y H:i:s');
    }

    /**
     * Get human readable time difference
     */
    public function getTimeAgoAttribute()
    {
        return $this->timestamp->diffForHumans();
    }

    /**
     * Scope to get recent activities
     */
    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('timestamp', 'desc')->limit($limit);
    }

    /**
     * Scope to filter by action type
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get delivery related activities
     */
    public function scopeDeliveryActivities($query)
    {
        return $query->whereIn('action', [
            'delivery_assigned',
            'delivery_started', 
            'delivery_completed',
            'delivery_proof_uploaded'
        ]);
    }
}