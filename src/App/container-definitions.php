<?php

declare(strict_types=1);

use Framework\{TemplateEngine, Database, Container};
use App\Services\{AuthService,
    RefreshTokenService,
    ValidatorService,
    UserService,
    TransactionService,
    JWTCodecService,
    TicketService};
use ZammadAPIClient\Client;
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
        $JWTCodec = $container->get(JWTCodecService::class);
        $refreshTokenService = $container->get(RefreshTokenService::class);
        return new UserService($db, $JWTCodec, $refreshTokenService);
    },
    TicketService::class => function (Container $container) {
        $db = $container->get(Database::class);
        $client = $container->get(Client::class);
        $userService = $container->get(UserService::class);
        return new TicketService($db, $client, $userService);
    },

    TransactionService::class => function (Container $container) {
        $db = $container->get(Database::class);
        return new TransactionService($db);
    },

    Client::class => fn() => new Client([
        'url' => $_ENV['ZAMMAD_URL'],
        'username' => $_ENV['ZAMMAD_USERNAME'],
        'password' => $_ENV['ZAMMAD_PASSWORD'],
    ]),

    AuthService::class => function (Container $container) {
        $JWTCodec = $container->get(JWTCodecService::class);
        $userService = $container->get(UserService::class);
        return new AuthService($JWTCodec, $userService);
    },

    RefreshTokenService::class => function (Container $container) {
        $db = $container->get(Database::class);
        return new RefreshTokenService($db, $_ENV['SECRET_KEY']);
    },
];