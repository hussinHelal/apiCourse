<?php

namespace App\Providers;

use App\Mail\Transport\MailtrapApiTransport;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
class MailtrapServiceProvider extends ServiceProvider
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
         Mail::extend('mailtrap-api', function (array $config) {
            return new MailtrapApiTransport(
                config('services.mailtrap.api_token'),
                config('services.mailtrap.inbox_id')
            );
        });
    }
}
