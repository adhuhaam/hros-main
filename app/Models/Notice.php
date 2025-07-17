<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'status',
        'publish_date',
        'expiry_date',
        'is_featured',
        'target_audience',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'publish_date' => 'date',
        'expiry_date' => 'date',
        'is_featured' => 'boolean',
    ];

    /**
     * Get the user who created the notice
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the notice
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope to get published notices
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'Published')
                    ->where(function ($q) {
                        $q->whereNull('publish_date')
                          ->orWhere('publish_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                    });
    }

    /**
     * Scope to get featured notices
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope to get notices by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get urgent notices
     */
    public function scopeUrgent($query)
    {
        return $query->where('type', 'Urgent');
    }

    /**
     * Check if notice is published
     */
    public function isPublished()
    {
        return $this->status === 'Published' &&
               ($this->publish_date === null || $this->publish_date <= now()) &&
               ($this->expiry_date === null || $this->expiry_date >= now());
    }

    /**
     * Check if notice is expired
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * Check if notice is urgent
     */
    public function isUrgent()
    {
        return $this->type === 'Urgent';
    }
} 