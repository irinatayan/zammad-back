<?php

declare(strict_types=1);

namespace App\Config;

use Framework\App;
use App\Middleware\{AuthRequiredMiddleware,
    CsrfGuardMiddleware,
    SessionMiddleware,
    TemplateDataMiddleware,
    UserMiddleware,
    ValidationExceptionMiddleware,
    FlashMiddleware,
    CsrfTokenMiddleware};

function registerMiddleware(App $app): void
{
//    $app->addMiddleware(CsrfGuardMiddleware::class );
//    $app->addMiddleware(CsrfTokenMiddleware::class );
    $app->addMiddleware(ValidationExceptionMiddleware::class );
//    $app->addMiddleware(AuthRequiredMiddleware::class);
//    $app->addMiddleware(FlashMiddleware::class);
//    $app->addMiddleware(SessionMiddleware::class );
//    $app->addMiddleware(UserMiddleware::class );
}