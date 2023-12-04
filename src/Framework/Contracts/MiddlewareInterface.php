<?php

declare(strict_types=1);

namespace App\Framework\Contracts;

interface MiddlewareInterface
{
    public function process(callable $next);
}