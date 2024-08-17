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
     * Parse CLI arguments
     *
     * @param array $argv
     * @return array
     */
    public function parseArguments(array $argv): array
    {
        $options = [];
        $currentKey = null;

        foreach ($argv as $index => $arg) {
            if ($index === 0) {
                continue; // Skip the script name
            }

            if (preg_match('/^--(.+)$/', $arg, $matches)) {
                // If the argument starts with '--', it's a key
                $currentKey = $matches[1];
                $options[$currentKey] = true; // Default value is true
            } elseif (preg_match('/^-([a-zA-Z])$/', $arg, $matches)) {
                // If argument starts with '-', it's a short key
                $currentKey = $matches[1];
                $options[$currentKey] = true; // Default value is true
            } elseif ($currentKey) {
                // If there's a current key, this argument is its value
                $options[$currentKey] = $arg;
                $currentKey = null; // Reset current key
            }
        }

        return $options;
    }
}
