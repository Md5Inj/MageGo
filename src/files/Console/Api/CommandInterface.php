<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Api;

interface CommandInterface
{
    /**
     * Execute command
     *
     * @param array $arguments
     * @return void
     */
    public function execute(array $arguments = []): void;

    /**
     * Return help text
     *
     * @return string
     */
    public function getHelpText(): string;

    /**
     * Return required parameters
     *
     * @return array
     */
    public function getRequiredParameters(): array;
}
