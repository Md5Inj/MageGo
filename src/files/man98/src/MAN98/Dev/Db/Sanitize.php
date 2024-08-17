<?php

namespace MAN98\Dev\Db;

use MAN98\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Sanitize extends AbstractCommand
{
    /**
     * Add CLI command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('dev:db:sanitize')
            ->setDescription('Sanitize DB');
    }

    /**
     * Template for sanitizer command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->detectMagento($output);
        $output->writeln('Sanitizing database...');

        // @TODO do some actions here

        return 0;
    }
}
