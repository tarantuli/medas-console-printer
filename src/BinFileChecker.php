<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter;

readonly class BinFileChecker
{
    public function check(): void
    {
        $workingDirectory = getcwd();
        $projectConsolePath = $workingDirectory . '/bin/console';
        $projectMedasPath = $workingDirectory . '/bin/medas';

        if (is_file($projectConsolePath)) {
            printf(
                "Found the deprecated console file at %s. Please remove it.\n",
                $projectConsolePath
            );
        }

        if (!is_file($projectMedasPath)) {
            printf(
                "Could not find the console file at %s. Please copy it from the vendor package.\n",
                $projectMedasPath
            );

            return;
        }

        $vendorMedasPath = realpath(__DIR__ . '/../bin/medas');

        if ($vendorMedasPath === false) {
            printf("Could not locate the vendor bin/medas file to compare against.\n");

            return;
        }

        if (file_get_contents($vendorMedasPath) !== file_get_contents($projectMedasPath)) {
            printf(
                "The content of bin/medas in %s differs from the vendor copy at %s. Please update it.\n",
                $workingDirectory,
                $vendorMedasPath
            );
        }
    }
}
