<?php

namespace Intune\LaravelPaystack\Providers;

use Illuminate\Support\ServiceProvider;
use Intune\LaravelPaystack\PaystackService;

/**
 * Class PaystackServiceProvider
 *
 * This service provider is responsible for registering the Paystack service 
 * and publishing its configuration file.
 *
 * @package YourVendor\MyPackage
 */
class PaystackServiceProvider extends ServiceProvider
{
   /**
    * Register the Paystack service in the application container.
    *
    * This method merges the package configuration with the application's 
    * configuration and binds the PaystackService to the service container 
    * as a singleton.
    *
    * @return void
    */
   public function register()
   {
      $this->mergeConfigFrom(__DIR__ . '/../../config/paystack.php', 'paystack');

      $this->app->singleton(PaystackService::class, function ($app) {
         return new PaystackService();
      });
   }

   /**
    * Boot the service provider.
    *
    * This method publishes the Paystack configuration file to the 
    * application's configuration directory.
    *
    * @return void
    */
   public function boot()
   {
      $this->publishes([
         __DIR__ . '/../../config/paystack.php' => config_path('paystack.php'),
      ]);
   }
}
