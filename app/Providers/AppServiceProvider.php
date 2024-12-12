<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\AgendaModel;
use App\Observers\AgendaObserver;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('menuactive', function ($expression) {
            return "<?php echo $expression === \$activemenu ? 'active' : ''; ?>";
        });

        Blade::directive('menuopen', function ($expression) {
            return "<?php echo in_array(\$activemenu, $expression) ? 'menu-open' : ''; ?>";
        });

        AgendaModel::observe(AgendaObserver::class);
    }
}