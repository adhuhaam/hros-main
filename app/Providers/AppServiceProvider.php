<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set default string length for MySQL
        Schema::defaultStringLength(191);

        // Set timezone to Maldivian time
        date_default_timezone_set('Indian/Maldives');
        Carbon::setLocale('en');

        // Share common data with all views
        View::composer('*', function ($view) {
            $view->with('appName', config('app.name'));
            $view->with('companyName', config('hros.company_name'));
            $view->with('companyAddress', config('hros.company_address'));
            $view->with('companyPhone', config('hros.company_phone'));
            $view->with('companyEmail', config('hros.company_email'));
        });

        // Custom Blade directives
        Blade::directive('role', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasRole({$expression})): ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('permission', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasPermissionTo({$expression})): ?>";
        });

        Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });

        // Custom helper functions
        if (!function_exists('format_date')) {
            function format_date($date, $format = 'd-M-Y') {
                if (!$date) return '';
                return \Carbon\Carbon::parse($date)->format($format);
            }
        }

        if (!function_exists('format_datetime')) {
            function format_datetime($datetime, $format = 'd-M-Y H:i') {
                if (!$datetime) return '';
                return \Carbon\Carbon::parse($datetime)->format($format);
            }
        }

        if (!function_exists('format_currency')) {
            function format_currency($amount, $currency = 'MVR') {
                return number_format($amount, 2) . ' ' . $currency;
            }
        }

        if (!function_exists('get_employment_status_badge')) {
            function get_employment_status_badge($status) {
                $badges = [
                    'Active' => 'badge-success',
                    'RESIGNED' => 'badge-warning',
                    'TERMINATED' => 'badge-danger',
                    'RETIRED' => 'badge-info',
                    'DEAD' => 'badge-dark',
                    'MISSING' => 'badge-secondary'
                ];
                
                return $badges[$status] ?? 'badge-secondary';
            }
        }

        if (!function_exists('get_leave_status_badge')) {
            function get_leave_status_badge($status) {
                $badges = [
                    'Pending' => 'badge-warning',
                    'Approved' => 'badge-success',
                    'Rejected' => 'badge-danger',
                    'Cancelled' => 'badge-secondary'
                ];
                
                return $badges[$status] ?? 'badge-secondary';
            }
        }
    }
}