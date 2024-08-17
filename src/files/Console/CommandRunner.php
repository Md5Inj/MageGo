<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console;

use MaksimRamashka\Deploy\Console\Api\CommandInterface;
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
     * Execute commands
     *
     * @param array $argv
     * @return void
     */
    public function run(array $argv): void
    {
        foreach ($argv as $parameter) {
            if ($parameter === '--help') {
                $argv['commands'] = $this->commands;
            }

            $command = $this->getCommandByParameterName($parameter);
            if ($command !== null) {
                $command->execute($argv);
                exit(0);
            }
        }

        if (count($argv) > 1) {
            $this->output->writeError(
                'Invalid argument or argument set: ' .  implode(', ', array_slice($argv, 1))
            );
        }

        $this->getCommandByParameterName('--help')->execute(['commands' => $this->commands]);
    }
}
