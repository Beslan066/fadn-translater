<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'can.register' => \App\Http\Middleware\CanRegister::class,
            'checkRole' =>  \App\Http\Middleware\CheckRole::class,
        ]);
        $middleware->alias([
            'isTranslator' => \App\Http\Middleware\RoleTranslator::class,
            'isProofreader' => \App\Http\Middleware\RoleProofreader::class,
            'region_admin' => \App\Http\Middleware\RegionAdmin::class,
            'home'  => \App\Http\Middleware\Home::class,
        ]);


    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
