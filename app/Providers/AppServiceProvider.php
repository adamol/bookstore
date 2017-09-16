<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\OrderConfirmationNumberGenerator;
use App\InventoryCodeGenerator;
use App\RandomOrderConfirmationNumberGenerator;
use App\HashidsInventoryCodeGenerator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(HashidsInventoryCodeGenerator::class, function() {
            return new HashidsInventoryCodeGenerator(config('app.inventory_code_salt'));
        });
        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);
        $this->app->bind(InventoryCodeGenerator::class, HashidsInventoryCodeGenerator::class);
    }
}
