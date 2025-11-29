<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Product;
use App\Models\Buyer;
use App\Models\Seller;
use App\Models\Transactions;
use App\Models\Categories;
use App\Observers\ProductObserver;
use App\Observers\CategoriesObserver;
use App\Observers\TransactionsObserver;
use App\Observers\BuyersObserver;
use App\Observers\SellersObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Mail\TestMail;
use App\Mail\userMailChanged;
use Laravel\Passport\Passport;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Passport\Contracts\AuthorizationViewResponse;
use App\Http\Responses\AuthorizationViewResponse as CustomAuthorizationViewResponse;



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
        Passport::enablePasswordGrant();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

         $this->app->bind(
        AuthorizationViewResponse::class,
        CustomAuthorizationViewResponse::class
        );

        RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(20)
                ->by($request->user()?->id ?: $request->ip());
        });


        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'mail');

        View::addNamespace('mail', [
            resource_path('views/vendor/mail'),
            __DIR__.'/../../vendor/laravel/framework/src/Illuminate/Mail/resources/views'
        ]);

        Product::updated(function ($product) {
            if ($product->quantity == 0 && $product->isAvailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;
                $product->save();
            }
        });

        User::created(function ($user) {
            retry(5, function () use ($user) {
            try {
                Mail::to($user->email)->queue(new TestMail($user));
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email to user ID ' . $user->id . ': ' . $e->getMessage());
            }
        }, 200);
        });

        User::updated(function ($user) {
            retry(5, function () use ($user) {
            try {
                if ($user->isDirty('email')) {
                    Mail::to($user->email)->queue(new userMailChanged($user));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send email change confirmation to user ID ' . $user->id . ': ' . $e->getMessage());
            }
        }, 200);
        });

        Product::observe(ProductObserver::class);
        Categories::observe(CategoriesObserver::class);
        Transactions::observe(TransactionsObserver::class);
        Buyer::observe(BuyersObserver::class);
        Seller::observe(SellersObserver::class);
    }
}
