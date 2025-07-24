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
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        use App\Exceptions\InsufficientBalanceException;
        use Illuminate\Http\Request;

        $exceptions->render(function (InsufficientBalanceException $e, Request $request) {
            return response()->json([
                'message' => 'Insufficient balance',
                'code' => 'INSUFFICIENT_BALANCE',
            ], 400);
        });
    })->create();
