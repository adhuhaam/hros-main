<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'type',
        'description',
        'is_recurring',
        'recurring_month',
        'recurring_day',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'recurring_month' => 'integer',
        'recurring_day' => 'integer',
    ];

    /**
     * Scope to get holidays for a specific year
     */
    public function scopeForYear($query, $year)
    {
        return $query->whereYear('date', $year);
    }

    /**
     * Scope to get upcoming holidays
     */
    public function scopeUpcoming($query, $days = 30)
    {
        return $query->where('date', '>=', now())
                    ->where('date', '<=', now()->addDays($days))
                    ->orderBy('date');
    }

    /**
     * Scope to get recurring holidays
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    /**
     * Check if holiday is today
     */
    public function isToday()
    {
        return $this->date->isToday();
    }

    /**
     * Check if holiday is upcoming
     */
    public function isUpcoming($days = 7)
    {
        return $this->date->isBetween(now(), now()->addDays($days));
    }
} 