<?php

declare(strict_types=1);

use Framework\{TemplateEngine, Database, Container};
use App\Services\{ValidatorService, UserService, TransactionService, JWTCodecService};
use App\Config\Paths;

return [
    TemplateEngine::class => fn() => new TemplateEngine(Paths::VIEW),
    ValidatorService::class => fn() => new ValidatorService(),
    JWTCodecService::class => fn() => new JWTCodecService($_ENV['SECRET_KEY']),
    Database::class => fn() => new Database(
        $_ENV['DB_DRIVER'],
        [
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_NAME']
        ],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    ),
    UserService::class => function (Container $container) {
        $db = $container->get(Database::class);
        $JWTCodec = $container->get(JWTCodecService::class );
        return new UserService($db, $JWTCodec);
    },
    TransactionService::class => function (Container $container) {
        $db = $container->get(Database::class);
        return new TransactionService($db);
    },
];