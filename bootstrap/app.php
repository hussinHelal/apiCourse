<?php

use App\Http\Middleware\transformInput;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Session\TokenMismatchException;
// use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;
use Illuminate\Auth\AuthenticationException;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(App\Http\Middleware\SignatureMiddleware::class);
        // $middleware->append(App\Http\Middleware\transformInput::class);
        // $middleware->alias(['transform'=>transformInput::class]);
           $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->api(prepend: [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // // Middleware aliases
        // $middleware->alias([
        //     'auth' => \App\Http\Middleware\Authenticate::class,
        //     'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        //     // Add your custom aliases here
        // ]);
        $middleware->alias([
            'signature' => \App\Http\Middleware\SignatureMiddleware::class,
            'client' => \Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner::class,
        ]);
        // // Configure throttling
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {

      $exceptions->render(function (TokenMismatchException $e, Request $request) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                    ], 419);
                }

                return redirect()->back()->withInput($request->except('_token'))->withErrors([
                    'error' => 'Your session has expired. Please try again.'
                ]);
            });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthenticated.',
                    'error' => 'authentication_required'
                ], 401);
            }
        });

        // Handle 404 Not Found exceptions
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The requested resource could not be found.',
                ], 404);
            }
        });

        // Handle other HTTP exceptions (401, 403, 500, etc.)
        $exceptions->render(function (HttpException $e, Request $request) {

         $status_code = $e->getStatusCode();
         if($status_code === 419)
         {
           if ($request->expectsJson()) {
               return response()->json([
                   'success' => false,
                   'message' => 'try again.',
               ], 419);
           }

           return redirect()->back()->withInput();
         }


            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'An error occurred.',
                ], $e->getStatusCode());
            }
        });

        // Handle general exceptions (fallback)
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->expectsJson()) {
                // Don't expose sensitive information in production
                $message = app()->environment('production')
                    ? 'Something went wrong.'
                    : $e->getMessage();

                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 500);
            }
        });


    })->create();
