<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands\ArgumentParser;

use Medas\ConsolePrinter\Exceptions\MultipleShortCodesWithValueGiven;
use Medas\Core\Attributes\Service;

#[Service]
readonly class PartsProcessor
{
    /**
     * @return array{0: string[], 1: array<string, string|bool>}
     */
    public function process(array $parts): array
    {
        $afterDoubleDash = false;
        $arguments = [];
        $options = [];

        foreach ($parts as $part) {
            if ($afterDoubleDash) {
                $arguments[] = $part;

                continue;
            }

            if ($part === '--') {
                $afterDoubleDash = true;

                continue;
            }

            if (str_starts_with($part, '--')) {
                $option = substr($part, 2);

                if (str_contains($option, '=')) {
                    [$name, $value] = explode('=', $option, 2);
                }
                else {
                    $name = $option;
                    $value = true;
                }

                $options[$name] = $value;

                continue;
            }

            if (str_starts_with($part, '-')) {
                $option = substr($part, 1);
                [$name, $value] = explode('=', $option, 2);

                if (strlen($name) > 1) {
                    if ($value !== null) {
                        throw new MultipleShortCodesWithValueGiven($name, $value);
                    }

                    $letters = str_split($name);

                    foreach ($letters as $letter) {
                        $options[$letter] = true;
                    }
                }
                else {
                    $options[$name] = $value ?? true;
                }

                continue;
            }

            $arguments[] = $part;
        }

        return [$arguments, $options];
    }
}
