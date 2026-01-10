<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // HAPUS METHOD getQrCodeAttribute() DULU
    
    // Bisa pindahkan ke Controller atau View
    // atau gunakan cara alternatif:
    
    /**
     * Get the QR code using external service (no package needed)
     */
    public function getQrCodeUrlAttribute($size = 200): string
    {
        $apiUrl = url("/api/cart/{$this->device_id}");
        $encodedUrl = urlencode($apiUrl);
        
        // Gunakan external QR code service
        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedUrl}";
    }

    /**
     * Get the API URL for this device.
     */
    public function getApiUrlAttribute(): string
    {
        return url("/api/cart/{$this->device_id}");
    }

    /**
     * Scope a query to only include active devices.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive devices.
     */
    public function scopeInactive(Builder $query): void
    {
        $query->where('status', 'inactive');
    }
}