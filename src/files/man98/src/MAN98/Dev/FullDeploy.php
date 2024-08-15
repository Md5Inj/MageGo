<?php
namespace MAN98\Dev;

use MAN98\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FullDeploy extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setName('dev:deploy:full')
            ->addArgument('branch', InputArgument::OPTIONAL, 'Branch name', 'master')
            ->setDescription('Do the full deployment process');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);

        $this->executeCommand('dev:git:branch ' . $input->getArgument('branch'), $output);

        $output->writeln('→ composer install');
        $this->process(['composer', 'install']);

        $this->executeCommand('dev:install:user', $output);
        $this->executeCommand('setup:upgrade', $output);
        $this->executeCommand('setup:di:compile', $output);

        return 0;
    }
}
