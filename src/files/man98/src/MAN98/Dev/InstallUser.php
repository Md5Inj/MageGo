<?php
namespace MAN98\Dev;

use MAN98\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallUser extends AbstractCommand
{
    protected function configure(): void
    {
        $this->setName('dev:install:user')
            ->setDescription('Do the deployment process');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->detectMagento($output);

        $this->executeCommand('admin:user:delete admin -f', $output);
        $this->executeCommand('admin:user:create --admin-user admin --admin-email admin@test.com --admin-password admin123 --admin-firstname Admin --admin-lastname Admin', $output);

        return 0;
    }
}
