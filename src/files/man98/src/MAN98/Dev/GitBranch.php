<?php

namespace MAN98\Dev;

use MAN98\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GitBranch extends AbstractCommand
{

    protected function configure()
    {
        $this->setName('dev:git:branch')
             ->addArgument('branch', InputArgument::OPTIONAL, 'Branch name', 'master')
             ->setDescription('Change (update) git branch and update all stuff after that');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);

        $this->executeCommand('sys:maintenance --on', $output);
        $output->writeln("<comment>Updating branch {$input->getArgument('branch')}</comment>");
        $commands = [
            ['git', 'stash'],
            ['git', 'fetch', '--all'],
            ['git', 'checkout', '-f', 'origin/' . $input->getArgument('branch')],
            ['git', 'checkout', '-f', $input->getArgument('branch')],
            ['git', 'pull']
        ];

        foreach ($commands as $command) {
            $output->writeln('→ ' . implode(' ', $command));
            $this->process($command);
        }

        $this->executeCommand('sys:maintenance --off', $output);

        return 0;
    }

}
