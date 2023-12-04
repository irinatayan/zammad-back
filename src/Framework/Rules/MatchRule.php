<?php

declare(strict_types=1);

namespace App\Framework\Rules;

use App\Framework\Contracts\RuleInterface;

class MatchRule implements RuleInterface
{

    public function validate(array $data, string $field, array $params): bool
    {
        $fieldOne = $data[$field];
        $fieldTwo = $data[$params[0]];

        return $fieldOne === $fieldTwo;
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Does not match {$params[0]} field.";
    }
}