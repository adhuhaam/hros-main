<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = $this->getSettings();
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:500',
            'company_website' => 'nullable|url|max:255',
            'timezone' => 'required|string|max:255',
            'date_format' => 'required|string|max:50',
            'time_format' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'default_language' => 'required|string|max:10',
            'pagination_limit' => 'required|integer|min:5|max:100',
            'session_timeout' => 'required|integer|min:5|max:480',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'maintenance_mode' => 'boolean',
        ]);

        // Update settings
        foreach ($validated as $key => $value) {
            $this->setSetting($key, $value);
        }

        // Clear cache
        Cache::forget('system_settings');

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully.');
    }

    /**
     * Get all settings
     */
    private function getSettings()
    {
        return Cache::remember('system_settings', 3600, function () {
            return [
                'company_name' => $this->getSetting('company_name', 'HR Management System'),
                'company_email' => $this->getSetting('company_email', 'admin@company.com'),
                'company_phone' => $this->getSetting('company_phone', ''),
                'company_address' => $this->getSetting('company_address', ''),
                'company_website' => $this->getSetting('company_website', ''),
                'timezone' => $this->getSetting('timezone', 'UTC'),
                'date_format' => $this->getSetting('date_format', 'Y-m-d'),
                'time_format' => $this->getSetting('time_format', 'H:i:s'),
                'currency' => $this->getSetting('currency', 'USD'),
                'currency_symbol' => $this->getSetting('currency_symbol', '$'),
                'default_language' => $this->getSetting('default_language', 'en'),
                'pagination_limit' => $this->getSetting('pagination_limit', 15),
                'session_timeout' => $this->getSetting('session_timeout', 120),
                'email_notifications' => $this->getSetting('email_notifications', true),
                'sms_notifications' => $this->getSetting('sms_notifications', false),
                'maintenance_mode' => $this->getSetting('maintenance_mode', false),
            ];
        });
    }

    /**
     * Get a setting value
     */
    private function getSetting($key, $default = null)
    {
        // In a real application, you might store settings in a database
        // For now, we'll use cache/file storage
        $settingsFile = storage_path('app/settings.json');
        
        if (file_exists($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true);
            return $settings[$key] ?? $default;
        }
        
        return $default;
    }

    /**
     * Set a setting value
     */
    private function setSetting($key, $value)
    {
        $settingsFile = storage_path('app/settings.json');
        
        $settings = [];
        if (file_exists($settingsFile)) {
            $settings = json_decode(file_get_contents($settingsFile), true) ?? [];
        }
        
        $settings[$key] = $value;
        
        file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT));
    }
} 