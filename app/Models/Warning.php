<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'warning_type',
        'subject',
        'description',
        'warning_date',
        'issued_by',
        'status',
        'severity_level',
        'action_taken',
        'improvement_plan',
        'follow_up_date',
        'acknowledged_at',
        'acknowledged_by',
    ];

    protected $casts = [
        'warning_date' => 'date',
        'follow_up_date' => 'date',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the warning.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the user who issued the warning.
     */
    public function issuer()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Get the user who acknowledged the warning.
     */
    public function acknowledger()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Scope for active warnings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for acknowledged warnings.
     */
    public function scopeAcknowledged($query)
    {
        return $query->whereNotNull('acknowledged_at');
    }

    /**
     * Scope for pending acknowledgments.
     */
    public function scopePendingAcknowledgment($query)
    {
        return $query->whereNull('acknowledged_at');
    }

    /**
     * Scope for warnings by severity level.
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity_level', $severity);
    }

    /**
     * Check if warning is acknowledged.
     */
    public function isAcknowledged()
    {
        return !is_null($this->acknowledged_at);
    }

    /**
     * Check if warning is active.
     */
    public function isActive()
    {
        return $this->status === 'Active';
    }

    /**
     * Get severity level color.
     */
    public function getSeverityColorAttribute()
    {
        switch ($this->severity_level) {
            case 'Low':
                return 'green';
            case 'Medium':
                return 'yellow';
            case 'High':
                return 'orange';
            case 'Critical':
                return 'red';
            default:
                return 'gray';
        }
    }

    /**
     * Acknowledge the warning.
     */
    public function acknowledge($userId)
    {
        $this->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => $userId,
        ]);
    }
}