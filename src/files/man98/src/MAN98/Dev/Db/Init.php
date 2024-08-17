<?php

namespace MAN98\Dev\Db;

use MAN98\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends AbstractCommand
{
    /**
     * Add CLI command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('dev:db:init')
            ->setDescription('Install fresh database and run post import actions');
    }

    /**
     * Install a fresh database and run post-import actions
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->detectMagento($output);

        $this->executeCommand('sys:maintenance --on', $output);
        $this->executeCommand('dev:db:install', $output);
        $this->executeCommand('dev:db:sanitize', $output);
        $this->executeCommand('dev:install:user', $output);
        $this->executeCommand('sys:maintenance --off', $output);

        return 0;
    }
}
