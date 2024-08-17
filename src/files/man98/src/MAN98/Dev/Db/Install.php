<?php

namespace MAN98\Dev\Db;

use MAN98\AbstractCommand;
use mysqli;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Install extends AbstractCommand
{
    /**
     * Add CLI command
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('dev:db:install')
            ->addOption('db-path', 'd', InputOption::VALUE_OPTIONAL, 'Custom dump path.', 'db')
            ->setDescription('Import database dump');
    }

    /**
     * Install a database from dumps directory
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->detectMagento($output);
        $start = microtime(true);
        $env = $this->_getEnv($input);

        $output->writeln('<comment>Installing latest dump!!!</comment>');

        if (!$dbConfig = $this->_getDB($env)) {
            $output->writeln('<error>You have no db configured in app/etc/env.php</error>');
        }

        $output->writeln('→ Check connection');
        $mysqli = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['dbname'], (int) $dbConfig['port']);
        if ($mysqli->connect_errno) {
            $output->writeln("<error>Connection error: {$mysqli->connect_error}</error>");

            return 1;
        }

        $mysqlConnection = [
            "mysql",
            "-h{$dbConfig['host']}",
            "-u{$dbConfig['username']}",
            "-p{$dbConfig['password']}",
            "-P{$dbConfig['port']}",
            $dbConfig["dbname"]
        ];

        $output->writeln(' ★ Dropping tables');
        $this->dropOldTables($mysqlConnection, $dbConfig['dbname']);

        $output->writeln(' ★ Installing db ' . $dbConfig['dbname']);

        $dump = $this->_magentoRootFolder . "/dumps/dump";

        $this->process(["rm","-rf", "$dump.sql*"]);
        Process::fromShellCommandline("gunzip -dc $dump.sql.gz > $dump.sql")->run();
        /** fix dump with sed */
        $this->process(["sed", "s/\sDEFINER=`[^`]*`@`[^`]*`//g", "-i", "$dump.sql"]);
        shell_exec('pv ' . $dump . '.sql | ' . implode(' ', $mysqlConnection));
        $this->process(["rm", "-rf", "$dump.sql"]);

        $output->writeln("<info>Database <comment>{$dbConfig['dbname']}</comment> successfully installed!</info>");

        $time = gmdate('H:i:s',(int) microtime(true) - (int) $start);
        $output->writeln("Finished in <info>{$time}</info>");

        return 0;
    }

    /**
     * Drop old tables
     *
     * @param array $mysqlConnection
     * @param string $dbName
     * @return void
     */
    public function dropOldTables(array $mysqlConnection, string $dbName): void
    {
        $tables = $this->process([
            ...$mysqlConnection,
            "--silent",
            "--skip-column-names",
            "-e",
            "SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$dbName'"
        ]);

        $tables = explode("\n", $tables);
        $tablesList = [];
        foreach ($tables as $table) {
            $table = trim($table);
            if ($table) {
                $tablesList[] = explode("\t", $table);
            }
        }

        $dropCommand = [];
        $dropCommand[] = 'SET FOREIGN_KEY_CHECKS = 0';
        foreach ($tablesList as $table) {
            $tableName = $table[0];
            if (trim($tableName)) {
                $type = $table[1];
                if ($type == 'NULL') {
                    $dropCommand[] = "DROP VIEW `$tableName`";
                    continue;
                }

                $dropCommand[] = "DROP TABLE `$tableName`";
            }
        }

        $dropCommand[] = 'SET FOREIGN_KEY_CHECKS = 1';
        $dropCommand   = implode(";\n", $dropCommand);
        $this->process([
            ...$mysqlConnection,
            "-e",
            "{$dropCommand}"
        ]);
    }
}
