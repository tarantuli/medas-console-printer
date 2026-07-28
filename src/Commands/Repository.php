<?php

declare(strict_types=1);

namespace Medas\ConsolePrinter\Commands;

use Medas\Console\{CommandRepository, Commands\ConsoleCommand, Commands\ConsoleCommandGroup};
use Medas\ConsolePrinter\Exceptions;
use Medas\Core\{Attributes\Service, Interfaces\ImplementorFinder, Interfaces\PrimesCache};

#[Service]
class Repository implements CommandRepository, PrimesCache
{
    private array $groups;
    private array $processors;
    private array $aliases;

    public function __construct(
        private readonly ImplementorFinder $implementorFinder,
    )
    {
    }

    public function findAlias(string $command): ConsoleCommand|null
    {
        if (!isset($this->aliases)) {
            $this->aliases = cache(
                [$this::class, 'getAllAliases'],
                fn() => $this->findAllAliases()
            );
        }

        if (!array_key_exists($command, $this->aliases)) {
            return null;
        }

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return service($this->aliases[$command]);
    }

    private function findAllAliases(): array
    {
        $aliases = [];

        foreach ($this->getAllCommands() as $processor) {
            foreach ($processor->aliases() as $alias) {
                if (!preg_match('/^[a-z0-9][a-z0-9.-]*[a-z0-9]$/', $alias)) {
                    throw new Exceptions\FoundInvalidAlias($alias);
                }

                if (array_key_exists($alias, $aliases)) {
                    throw new Exceptions\FoundDuplicateAliases(
                        $alias,
                        $processor::class,
                        $aliases[$alias]
                    );
                }

                $aliases[$alias] = $processor::class;
            }
        }

        return $aliases;
    }

    /** @return ConsoleCommandGroup[] */
    public function getGroups(ConsoleCommandGroup|null $parent = null): array
    {
        $groups = [];

        foreach ($this->getAllGroups() as $group) {
            if ($group->parent() === $parent) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /** @return ConsoleCommandGroup[] */
    public function getAllGroups(): array
    {
        if (!isset($this->groups)) {
            $groupNames = cache(
                [$this::class, 'getAllGroupNames'],
                fn() => $this->implementorFinder->find(ConsoleCommandGroup::class)
            );

            $this->groups = [];

            foreach ($groupNames as $groupName) {
                $this->groups[] = service($groupName);
            }
        }

        return $this->groups;
    }

    /** @return ConsoleCommand[] */
    public function getProcessors(ConsoleCommandGroup $parent): array
    {
        $processors = [];

        foreach ($this->getAllCommands() as $processor) {
            if ($processor->group() === $parent) {
                $processors[] = $processor;
            }
        }

        return $processors;
    }

    /** @return ConsoleCommand[] */
    public function getAllCommands(): array
    {
        if (!isset($this->processors)) {
            $processorNames = cache(
                [$this::class, 'getAllProcessorNames'],
                fn() => $this->implementorFinder->find(ConsoleCommand::class)
            );

            $this->processors = [];

            foreach ($processorNames as $processorName) {
                $this->processors[] = service($processorName);
            }

            usort(
                $this->processors,
                fn(ConsoleCommand $a, ConsoleCommand $b)
                    => strcasecmp($a->fullCommand(), $b->fullCommand()
                )
            );
        }

        return $this->processors;
    }

    public function primeCache(): void
    {
        $this->getAllGroups();
        $this->getAllCommands();
    }
}
