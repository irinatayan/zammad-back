<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\Exceptions\ValidationException;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function process(callable $next): void
    {
        try {
            $next();
        } catch (ValidationException $e) {
            $oldFormData = $_POST;

            $excludedFields = ['password', 'confirmPassword'];

            $formattedFormData = array_diff_key(
                $oldFormData, array_flip($excludedFields)
            );

            http_response_code(500);
            echo json_encode([
                "message" => "invalid data"
            ]);
        }
    }
}