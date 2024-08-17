<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use MaksimRamashka\Deploy\Model\Output;

class HelpCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected string $commandName = '--help';

    /**
     * @var Output
     */
    private Output $output;

    /**
     * @var OutdatedCommand
     */
    private OutdatedCommand $outdatedCommand;

    public function __construct()
    {
        $this->output = new Output();
        $this->outdatedCommand = new OutdatedCommand();
    }

    /**
     * @inheritDoc
     */
    public function execute(array $arguments = []): void
    {
        if (!isset($arguments['commands'])) {
            exit(0);
        }

        $this->outdatedCommand->execute();
        $this->output->writeInfo($this->generateHelpText($arguments['commands']));
        exit(0);
    }

    /**
     * Generate help text
     *
     * @param array $commands
     * @return string
     */
    private function generateHelpText(array $commands): string
    {
        $helpText = "\nUsage: deploy_commands.phar [options]\n\n";
        $helpText .= "Available options:\n";
        foreach ($commands as $command) {
            $helpText .= $command->getHelpText();
        }
        return $helpText;
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "--help or no params: Display this help message\n";
    }
}
