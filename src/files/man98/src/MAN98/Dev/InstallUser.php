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
     * Add admin user
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->detectMagento($output);

        if ($this->isAdminUserExists()) {
            $output->writeln('<info>User "admin" already exists. Deleting...</info>');
            $this->executeCommand('admin:user:delete admin -f', $output);
        } else {
            $output->writeln('<info>User "admin" does not exist. Creating new user...</info>');
        }

        $this->executeCommand('admin:user:create --admin-user admin --admin-email admin@test.com --admin-password admin123 --admin-firstname Admin --admin-lastname Admin', $output);

        return 0;
    }

    /**
     * Checks if a user with the given username exists.
     *
     * @return bool
     */
    private function isAdminUserExists(): bool
    {
        $userList = $this->process([
            $_SERVER['SCRIPT_NAME'],
            'admin:user:list',
            '--format=json'
        ]);

        $userList = json_decode($userList, true);
        foreach ($userList as $user) {
            if ($user['username'] === 'admin') {
                return true;
            }
        }

        return false;
    }
}
