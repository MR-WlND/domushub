<?php

use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        api:__DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->api(prepend: [
            ForceJsonResponse::class
        ]);

        $middleware->alias([
            'admin'        => \App\Http\Middleware\AdminMiddleware::class,
            'manager'      => \App\Http\Middleware\ManagerMiddleware::class,
            'staff'        => \App\Http\Middleware\StaffMiddleware::class,
            'technician'   => \App\Http\Middleware\TechnicianMiddleware::class,
            'resident'     => \App\Http\Middleware\ResidentMiddleware::class,
            'security'     => \App\Http\Middleware\SecurityMiddleware::class,
            'cleaning'     => \App\Http\Middleware\CleaningMiddleware::class,
            'receptionist' => \App\Http\Middleware\ReceptionistMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
