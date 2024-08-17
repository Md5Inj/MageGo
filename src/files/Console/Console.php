<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console;

use MaksimRamashka\Deploy\Console\Commands\DeployCommand;
use MaksimRamashka\Deploy\Console\Commands\HelpCommand;
use MaksimRamashka\Deploy\Console\Commands\OutdatedCommand;
use MaksimRamashka\Deploy\Console\Commands\SelfUpdateCommand;
use MaksimRamashka\Deploy\Console\Commands\VerboseCommand;
use MaksimRamashka\Deploy\Console\Commands\VersionCommand;

class Console
{
    /**
     * Execute commands
     *
     * @param array $argv
     * @return void
     */
    public function execute(array $argv): void
    {
        $commands = [
            new HelpCommand(),
            new VerboseCommand(),
            new SelfUpdateCommand(),
            new VersionCommand(),
            new DeployCommand(),
            new OutdatedCommand()
        ];

        $runner = new CommandRunner($commands);
        $runner->run($argv);
    }
}
