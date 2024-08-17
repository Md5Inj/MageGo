<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console;

use Exception;
use MaksimRamashka\Deploy\Console\Commands\ConfigureGrunt;
use MaksimRamashka\Deploy\Console\Commands\ConfigureSsh;
use MaksimRamashka\Deploy\Console\Commands\ConfigureXdebug;
use MaksimRamashka\Deploy\Console\Commands\DeployCommand;
use MaksimRamashka\Deploy\Console\Commands\HelpCommand;
use MaksimRamashka\Deploy\Console\Commands\InstallCodingStandardsCommand;
use MaksimRamashka\Deploy\Console\Commands\OutdatedCommand;
use MaksimRamashka\Deploy\Console\Commands\SelfUpdateCommand;
use MaksimRamashka\Deploy\Console\Commands\VerboseCommand;
use MaksimRamashka\Deploy\Console\Commands\VersionCommand;
use MaksimRamashka\Deploy\Model\Output;

class Console
{
    /**
     * @var Output
     */
    private Output $output;

    public function __construct()
    {
        $this->output = new Output();
    }

    /**
     * Execute commands
     *
     * @param array $argv
     * @return void
     */
    public function execute(array $argv): void
    {
        try {
            $commands = [
                new HelpCommand(),
                new VerboseCommand(),
                new SelfUpdateCommand(),
                new VersionCommand(),
                new DeployCommand(),
                new OutdatedCommand(),
                new InstallCodingStandardsCommand(),
                new ConfigureXdebug(),
                new ConfigureSsh(),
                new ConfigureGrunt()
            ];

            $runner = new CommandRunner($commands);
            $runner->run($argv);
        } catch (Exception $e) {
            $this->output->writeError($e->getMessage());
        }
    }
}
