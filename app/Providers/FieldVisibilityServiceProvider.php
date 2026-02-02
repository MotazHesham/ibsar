<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\FieldVisibilityHelper;

class FieldVisibilityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade directive for field visibility
        Blade::directive('field', function ($expression) {
            return "<?php if(\\App\\Helpers\\FieldVisibilityHelper::shouldShowField($expression)): ?>";
        });

        Blade::directive('endfield', function () {
            return "<?php endif; ?>";
        });

        // Register Blade directive for field configuration
        Blade::directive('fieldconfig', function ($expression) {
            return "<?php echo json_encode(\\App\\Helpers\\FieldVisibilityHelper::getFieldConfig($expression)); ?>";
        });
    }
}
