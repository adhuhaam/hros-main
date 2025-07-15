<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'project_value',
        'client',
        'started_date',
        'end_date',
        'status',
        'images',
        'description',
    ];

    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'project_value' => 'decimal:2',
        'started_date' => 'date',
        'end_date' => 'date',
        'images' => 'array',
    ];

    /**
     * Get the employees assigned to this project.
     */
    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_project_allocations');
    }
} 