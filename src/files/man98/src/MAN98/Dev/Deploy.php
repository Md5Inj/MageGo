<?php
namespace MAN98\Dev;

use MAN98\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Deploy extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setName('dev:deploy')
            ->setDescription('Do the fast deployment process');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);

        $this->executeCommand('setup:upgrade', $output);
        $this->executeCommand('setup:di:compile', $output);

        return 0;
    }
}
