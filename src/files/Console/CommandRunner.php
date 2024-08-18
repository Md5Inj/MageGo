<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console;

use Exception;
use MaksimRamashka\Deploy\Console\Api\CommandInterface;
use MaksimRamashka\Deploy\Console\Commands\HelpCommand;
use MaksimRamashka\Deploy\Model\Output;

class CommandRunner
{
    /**
     * @var CommandInterface[]
     */
    private array $commands;

    /**
     * @var Output
     */
    private Output $output;

    /**
     * @param array $commands
     */
    public function __construct(array $commands)
    {
        $this->commands = $commands;
        $this->output = new Output();
    }

    /**
     * Execute commands
     *
     * @param array $argv
     * @return void
     */
    public function run(array $argv): void
    {
        $arguments = $this->parseArguments($argv);
        $command = $this->getCommandByParameterName($argv[1] ?? '');

        if ($command !== null) {
            try {
                $this->checkForRequiredParameters($command, $arguments);

                if ($command instanceof HelpCommand) {
                    $arguments['commands'] = $this->commands;
                }

                $command->execute($arguments);
            } catch (Exception $e) {
                $this->output->writeError($e->getMessage());
            }

            exit(0);
        }

        if (count($argv) > 1) {
            $this->output->writeError(
                'Invalid argument or argument set: ' .  implode(', ', array_slice($argv, 1))
            );
        }

        $this->getCommandByParameterName('--help')->execute(['commands' => $this->commands]);
    }

    /**
     * Return command by model by parameter name
     *
     * @param string $parameterName
     * @return CommandInterface|null
     */
    public function getCommandByParameterName(string $parameterName): ?CommandInterface
    {
        $commandModel = null;

        foreach ($this->commands as $command) {
            if ($command->getCommandParam() == $parameterName) {
                $commandModel = $command;
            }
        }

        return $commandModel;
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

    /**
     * @param CommandInterface $command
     * @param array $arguments
     * @return void
     * @throws Exception
     */
    private function checkForRequiredParameters(CommandInterface $command, array $arguments): void
    {
        foreach ($command->getRequiredParameters() as $requiredParameter) {
            if (!isset($arguments[$requiredParameter])) {
                throw new Exception("Required parameter '{$requiredParameter}' is missing");
            }
        }
    }
}
