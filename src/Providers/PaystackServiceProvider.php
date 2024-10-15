<?php

namespace YourVendor\MyPackage;

use Illuminate\Support\ServiceProvider;
use Intune\LaravelPaystack\PaystackService;

class PaystackServiceProvider extends ServiceProvider
{
   public function register()
   {
      $this->mergeConfigFrom(__DIR__ . '/../config/paystack.php', 'paystack');
      
      $this->app->singleton(PaystackService::class, function ($app) {
         return new PaystackService();
      });
   }

   public function boot()
   {
      $this->publishes([
         __DIR__ . '/../config/paystack.php' => config_path('paystack.php'),
      ]);
   }
}
