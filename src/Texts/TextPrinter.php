<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Texts;

use Medas\Console\{Formats\Format, Style, Text};
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
        [$text, $formats] = $this->resolve($block->text, $block->format);

        if ($formats === []) {
            echo $text;
        }
        else {
            $this->format($text, $formats);
        }
    }

    /**
     * Expands any {@see Style} found among the block's formats: its layout (width/align/
     * truncate/padding) is applied to the text right here, and its color/decoration is spliced
     * into the atomic format list as if it had been passed directly. Plain atomic formats
     * (`SafeColor`, `HexColor`, `Decoration`, ...) pass through unchanged, so existing callers
     * that never use `Style` are unaffected.
     *
     * @param  Format[]  $formats
     * @return array{0: string, 1: Format[]}
     */
    private function resolve(string $text, array $formats): array
    {
        $resolvedFormats = [];

        foreach ($formats as $format) {
            if ($format === null) {
                continue;
            }

            if ($format instanceof Style) {
                $text = $format->applyLayout($text);

                array_push($resolvedFormats, ...$format->colorFormats());

                continue;
            }

            $resolvedFormats[] = $format;
        }

        return [$text, $resolvedFormats];
    }

    /** @param Format[] $formats */
    private function format(string $string, array $formats): void
    {
        $codes = array_map(fn(Format $format) => $this->bashFormat->getCode($format), $formats);

        printf("\e[%sm%s\e[0m", implode(';', $codes), $string);
    }
}
