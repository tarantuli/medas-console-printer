<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Reader;

use Medas\Core\Attributes\Service;

#[Service]
readonly class InputValidators
{
    public function emailAddress(): \Closure
    {
        return fn(string $input) => filter_var($input, FILTER_VALIDATE_EMAIL);
    }

    public function string(int $minLength = 1, int $maxLength = null): \Closure
    {
        return fn(string $input) => strlen($input) >= $minLength && (
            $maxLength === null
            || strlen($input) <= $maxLength
        );
    }

    public function integer(int $minValue = 0, int $maxValue = null): \Closure
    {
        return fn(string $input) => preg_match('/^-?\d+$/', $input)
            && (int) $input >= $minValue
            && ($maxValue === null || (int) $input <= $maxValue);
    }

    public function boolean(): \Closure
    {
        return fn(string $input) => in_array($input, ['y', 'n'], true);
    }
}
