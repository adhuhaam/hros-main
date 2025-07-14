<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'medical_type',
        'test_date',
        'expiry_date',
        'status',
        'notes',
        'file_path',
        'test_center',
        'cost',
        'next_test_date',
    ];

    protected $casts = [
        'test_date' => 'date',
        'expiry_date' => 'date',
        'next_test_date' => 'date',
        'cost' => 'decimal:2',
    ];

    /**
     * Get the employee that owns the medical record.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'emp_no');
    }

    /**
     * Scope for active medical records.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for expired medical records.
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    /**
     * Scope for expiring soon medical records.
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    /**
     * Check if medical record is expired.
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if medical record is expiring soon.
     */
    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(now()) <= $days;
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->expiry_date) {
            return null;
        }
        
        return $this->expiry_date->diffInDays(now(), false);
    }
}