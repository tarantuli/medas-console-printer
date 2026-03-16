<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Texts;

use Medas\Console\{Formats\Format, Text};
use Medas\ConsolePrinter\Formats\BashFormat;
use Medas\Core\Attributes\Service;

#[Service]
readonly class TextPrinter
{
    public function __construct(
        private BashFormat $bashFormat,
    )
    {
    }

    public function print(Text $block): void
    {
        if ($block->format === []) {
            echo $block->text;
        }
        else {
            $this->format($block->text, $block->format);
        }
    }

    /** @param Format|Format[] $formats */
    private function format(string $string, mixed $formats): void
    {
        $codes = [];

        foreach (is_array($formats) ? $formats : [$formats] as $format) {
            if ($format === null) {
                continue;
            }

            $codes[] = $this->bashFormat->getCode($format);
        }

        printf("\e[%sm%s\e[0m", implode(';', $codes), $string);
    }
}
