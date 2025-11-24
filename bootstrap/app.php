<?php

use App\Http\Middleware\GlobalApiKey;
use App\Http\Middleware\SwitchBranch;
use Illuminate\Foundation\Application;
use App\Http\Middleware\IdentifyBranch;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\TrimStrings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->use([
            TrimStrings::class,
        ]);

        $middleware->alias([
            'global.apikey'   => GlobalApiKey::class,
            'identify.branch' => IdentifyBranch::class,
            'switch.branch'   => SwitchBranch::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        $middleware->priority([
            GlobalApiKey::class,
            IdentifyBranch::class,
            Authenticate::class,
            SwitchBranch::class,
        ]);

        $middleware->api(prepend: [
            GlobalApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            return response()->json([
                'responseMessage' => 'You do not have the required authorization.',
            ], 403);
        });
    })
    ->create();
