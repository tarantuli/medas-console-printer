<?php

declare(strict_types=1);

namespace Medas\ConsolePrinterTest\Functional;

class BinConsoleTest extends BaseTestClass
{
    public function testExecuteConsole(): void
    {
        $output = $this->execute('console:command-list');
        self::assertStringContainsString('Available', implode("\n", $output));
    }
}
