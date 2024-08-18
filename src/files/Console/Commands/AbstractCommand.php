<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use MaksimRamashka\Deploy\Console\Api\CommandInterface;

abstract class AbstractCommand implements CommandInterface
{
    protected string $commandName = '';

    /**
     * Return current command parameter name
     *
     * @return string
     */
    public function getCommandParam(): string
    {
        return $this->commandName;
    }

    /**
     * @inheritDoc
     */
    public function getRequiredParameters(): array
    {
        return [];
    }
}
