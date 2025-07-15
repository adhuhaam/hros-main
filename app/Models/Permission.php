<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
        'action',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get roles that have this permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    /**
     * Get users that have this permission through their roles.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_permissions', 'permission_id', 'role_id', 'id', 'role_id');
    }

    /**
     * Scope to get active permissions only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get permissions by module.
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Scope to get permissions by action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Get all permissions grouped by module.
     */
    public static function getGroupedByModule()
    {
        return self::active()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('module');
    }

    /**
     * Get permissions for a specific module.
     */
    public static function getByModule($module)
    {
        return self::active()
            ->byModule($module)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Check if permission exists by name.
     */
    public static function existsByName($name)
    {
        return self::where('name', $name)->exists();
    }

    /**
     * Create a new permission if it doesn't exist.
     */
    public static function findOrCreate($name, $attributes = [])
    {
        return self::firstOrCreate(['name' => $name], $attributes);
    }

    /**
     * Get permission by name.
     */
    public static function findByName($name)
    {
        return self::where('name', $name)->first();
    }

    /**
     * Get all permission names as array.
     */
    public static function getAllNames()
    {
        return self::active()->pluck('name')->toArray();
    }

    /**
     * Get permissions for a specific role.
     */
    public static function getForRole($roleId)
    {
        return self::whereHas('roles', function ($query) use ($roleId) {
            $query->where('roles.id', $roleId);
        })->get();
    }

    /**
     * Get permission names for a specific role.
     */
    public static function getNamesForRole($roleId)
    {
        return self::whereHas('roles', function ($query) use ($roleId) {
            $query->where('roles.id', $roleId);
        })->pluck('name')->toArray();
    }
}
