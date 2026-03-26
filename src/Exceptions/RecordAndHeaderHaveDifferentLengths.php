<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Exceptions;

use Medas\Core\Exceptions\BaseException;

class RecordAndHeaderHaveDifferentLengths extends BaseException
{
    public function __construct(int $index, array $record, array $header)
    {
        $recordStr = implode(
            ', ',
            array_map(fn(mixed $v) => is_string($v) ? $v : gettype($v), $record)
        );

        parent::__construct($index, $recordStr, implode(', ', $header));
    }

    public function pattern(): string
    {
        return 'Record %s with values [%s], and header [%s] have different lengths';
    }
}
