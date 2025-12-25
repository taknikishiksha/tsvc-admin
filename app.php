<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // ✅ Ab ye kaam karega
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // ✅ Middleware aliases register karo
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'teacher' => \App\Http\Middleware\TeacherMiddleware::class,
            'client' => \App\Http\Middleware\ClientMiddleware::class,
            'complete.profile' => \App\Http\Middleware\CompleteProfileMiddleware::class,
        ]);
        
        // Add your middleware groups
        $middleware->web(append: [
            // Add any web middleware here
        ]);

        $middleware->api(append: [
            // Add any API middleware here
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
