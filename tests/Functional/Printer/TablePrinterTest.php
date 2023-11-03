<?php

declare(strict_types=1);

namespace Medas\ConsolePrinterTest\Functional\Printer;

use Medas\Console\ConsolePackage;
use Medas\Console\Table;
use Medas\ConsolePrinter\Tables\TablePrinter;
use Medas\ConsolePrinterTest\Functional\BaseTestClass;
use Medas\ServiceManager\ServiceManagerPackage;

class TablePrinterTest extends BaseTestClass
{
    public function testBasicTest(): void
    {
        ob_start();
        service(TablePrinter::class)->print(Table::create(
            ['id', 'package'],
            [
                [1, ServiceManagerPackage::class],
                [2, ConsolePackage::class],
            ]
        ));

        $output = ob_get_clean();

        self::assertStringContainsString('Medas\\ServiceManager\\ServiceManagerPackage', $output);
        self::assertStringContainsString('─', $output);
    }
}
