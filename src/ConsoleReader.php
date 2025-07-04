<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

use Medas\Core\Attributes\Service;

#[Service]
readonly class ConsoleReader
{
    /**
     * @var resource
     */
    private mixed $stdin;

    public function __construct(
        private ConsolePrinter $printer,
    )
    {
        $this->stdin = fopen('php://stdin', 'r');
    }

    public function read(Reader\Options $options): string
    {
        if ($options->prompt) {
            $this->printer->print($options->prompt);
        }

        do {
            $input = fgets($this->stdin);

            if ($input === false) {
                $isValid = false;
            }
            else {
                if ($options->doTrim) {
                    $input = trim($input);
                }

                if ($input === '' && $options->default !== null) {
                    $input = $options->default;
                }

                $isValid = !$options->validator || ($options->validator)($input);
            }
        } while (!$isValid);

        return $input;
    }
}
