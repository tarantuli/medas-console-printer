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
        $stdin = fopen('php://stdin', 'r');

        if ($stdin === false) {
            throw new \RuntimeException('Failed to open stdin for reading.');
        }

        $this->stdin = $stdin;
    }

    public function __destruct()
    {
        if (is_resource($this->stdin)) {
            fclose($this->stdin);
        }
    }

    public function read(Reader\Options $options): string
    {
        if ($options->prompt) {
            $this->printer->print($options->prompt);
        }

        do {
            $input = fgets($this->stdin);

            if ($input === false) {
                throw new \RuntimeException('Failed to read from stdin: stream closed or EOF reached.');
            }

            if ($options->doTrim) {
                $input = trim($input);
            }

            if ($input === '' && $options->default !== null) {
                $input = $options->default;
            }

            $isValid = !$options->validator || ($options->validator)($input);
        } while (!$isValid);

        return $input;
    }
}
