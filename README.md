# medas-console-printer

Part of the [Medas framework](https://github.com/tarantuli/medas-core).

## Description

The terminal-rendering implementation for `medas-console`. It provides `ConsolePrinter` (the concrete `Printer`), `CommandProcessor` (the CLI entry point that resolves and dispatches commands), `ConsoleReader` (interactive stdin reading with validation), and `ExceptionPrinter` (coloured backtrace output).

**`ConsolePrinter`** implements `Printer` by delegating each `Printable` type to a dedicated sub-printer:

| Printable type | Rendered as |
|---|---|
| `Text` | ANSI-escaped coloured/styled terminal output via `BashFormat` |
| `Table` | Auto-sized columns with configurable separator width and left indent |
| `Tree` | Recursive indented structure via `TreePrinter` |
| `Diff` | Diff output with standard green/red line colouring |
| `Blocks` | Recursively renders each contained `Printable` |
| `null` | A configurable null glyph (default: `〜`) |

**`CommandProcessor`** is the entry point called from `bin/console`. It resolves the command from `$argv`, parses arguments and options via `ArgumentParser`, and calls `process()` on the found command. Command and group names support unambiguous prefix matching — typing the shortest unambiguous prefix of a name is enough to invoke it.

**`ConsoleReader`** reads a line from stdin, optionally printing a prompt first, trimming whitespace, applying a default for empty input, and re-prompting until a validator closure returns `true`. `InputValidators` ships ready-made validators for email addresses, strings (min/max length), integers (min/max value), and boolean yes/no.

## Configuration options

| Option | Default | Description |
|---|---|---|
| `console-printer.null-glyph` | `〜` | Symbol printed in place of `null` values |
| `console-printer.table-column-separator` | `3` | Spaces between table columns |
| `console-printer.table-left-indent` | `3` | Spaces to indent table rows from the left |

## Usage

### Package developer context

Register the package and use the injected `Printer` and `ConsoleReader` in commands:

```php
use Medas\ConsolePrinter\ConsolePrinterPackage;

ConsolePrinterPackage::instance();
```

**Printing from a command:**

```php
use Medas\Console\{Printer, Table, Text, Tree};
use Medas\Console\Formats\{SafeColor, Style};
use Medas\Console\Commands\{BaseConsoleCommand, CommandInput};
use Medas\Core\Attributes\Service;

#[Service]
readonly class MyCommand extends BaseConsoleCommand
{
    public function __construct(
        private Printer      $printer,
        private MyGroup      $group,
    ) {}

    // ... group(), name(), description()

    public function process(CommandInput $input): void
    {
        // Simple coloured line
        $this->printer->printLine(
            Text::create('Build complete ', SafeColor::Green, Style::Bold),
            Text::create('(3 warnings)', SafeColor::Orange),
        );

        // Table
        $this->printer->printLine(Table::create(
            headers: ['File', 'Lines', 'Status'],
            data: [
                [Text::create('src/Foo.php', SafeColor::LightGray), Text::create('120'), Text::create('OK', SafeColor::Green)],
                [Text::create('src/Bar.php', SafeColor::LightGray), Text::create('85'),  Text::create('Warning', SafeColor::Orange)],
            ],
        ));

        // Blank line
        $this->printer->printEol();
    }
}
```

**Reading interactive input:**

```php
use Medas\ConsolePrinter\{ConsoleReader, ExceptionPrinter};
use Medas\ConsolePrinter\Reader\{InputValidators, Options};
use Medas\Console\Text;
use Medas\Console\Formats\SafeColor;
use Medas\Core\Attributes\Service;

#[Service]
readonly class SetupCommand extends BaseConsoleCommand
{
    public function __construct(
        private ConsoleReader   $reader,
        private InputValidators $validators,
        private Printer         $printer,
        private MyGroup         $group,
    ) {}

    public function process(CommandInput $input): void
    {
        // Prompt for an email address; re-asks until valid
        $email = $this->reader->read(new Options(
            prompt: Text::create('Admin email: ', SafeColor::LightYellow),
            validator: $this->validators->emailAddress(),
        ));

        // Prompt for a port number with a default
        $port = $this->reader->read(new Options(
            prompt: Text::create('Port [8080]: ', SafeColor::LightYellow),
            validator: $this->validators->integer(minValue: 1, maxValue: 65535),
            default: '8080',
        ));

        // Yes/no confirmation; re-asks until 'y' or 'n'
        $confirm = $this->reader->read(new Options(
            prompt: Text::create('Proceed? [y/n]: ', SafeColor::LightYellow),
            validator: $this->validators->boolean(),
        ));

        if ($confirm !== 'y') {
            $this->printer->printLine(Text::create('Aborted.', SafeColor::LightRed));
            return;
        }

        // ... proceed with setup
    }
}
```

**Custom validator:**

```php
$input = $this->reader->read(new Options(
    prompt: Text::create('Slug: ', SafeColor::LightYellow),
    validator: fn(string $value) => (bool) preg_match('/^[a-z0-9-]+$/', $value),
));
```

**Using `ExceptionPrinter` as an exception handler:**

```php
use Medas\ConsolePrinter\ExceptionPrinter;

// Wire it up in your bootstrap or ServiceConfig
$config->addExceptionHandlerClasses(ExceptionPrinter::class);
```

When an uncaught exception reaches it, `ExceptionPrinter` prints the full reversed backtrace with file, line, class, method, and arguments, followed by the exception class and message. If the exception implements `Suggestions`, a suggestions block is printed below the message. It falls back to a plain `echo` if the `Printer` itself is unavailable.

**The `bin/console` entry point:**

```php
<?php
// bin/console — bootstrap your application, then delegate to CommandProcessor

require_once __DIR__ . '/../vendor/autoload.php';

// ... register packages, build service container

service(\Medas\ConsolePrinter\CommandProcessor::class)->process($argv);
```

`CommandProcessor` reads `$argv[0]` as the command token. If omitted it falls back to `console:command-list`. Group and command names support unambiguous prefix matching:

```bash
# Full name
php bin/medas config-options:list

# Unambiguous prefix — works as long as no other group starts with 'config-o'
php bin/medas config-o:l
```

### Backend user context

**Running a command:**

```bash
php bin/medas <group>:<command> [arguments] [options]

# Examples
php bin/medas console:command-list
php bin/medas reports:generate 2026-05 --format=csv --verbose
```

**Prefix matching** — you only need to type enough characters to be unambiguous:

```bash
# If 'reports' is the only group starting with 're', and 'generate' the only command starting with 'g':
php bin/medas re:g 2026-05
```

If the prefix is ambiguous (matches more than one group or command) the runner throws an error listing the candidates.

**Passing `--` to end option parsing:**

```bash
# Everything after -- is treated as a positional argument, not an option
php bin/medas search:run -- --literal-hyphen-value
```
