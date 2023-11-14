<?php

declare(strict_types=1);

namespace App;

class Request
{
    public function __construct(
        private array $params = []
    ) {
    }

    public function getParams(): array
    {
        return $this->params;
    }
}