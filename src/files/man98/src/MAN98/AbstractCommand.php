<?php

namespace MAN98;

require_once __DIR__ . '/../../../../vendor/autoload.php';

use N98\Magento\Command\AbstractMagentoCommand;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AbstractCommand extends AbstractMagentoCommand
{
    protected $_env;

    public const ENV_PATH = 'app/etc/env.php';

    /**
     * Load config file
     *
     * @param $name
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function loadConfig($name): array
    {
        $path = $this->_magentoRootFolder . '/dev/man98/config/' . $name . '.json';
        if (!file_exists($path)) {
            throw new \RuntimeException("Config file {$path} not found!");
        }

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Execute shell command
     *
     * @param array $command
     *
     * @return string
     */
    protected function process(array $command)
    {
        $process = new Process($command, null, null, null, null);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Executes command within current command
     *
     * @param                 $command
     * @param OutputInterface $output
     * @param bool            $exitOnError
     *
     * @return int
     */
    protected function executeCommand($command, OutputInterface $output, $exitOnError = true)
    {
        $output->writeln("<comment>Start command {$command}</comment>");
        $this->getApplication()->setAutoExit(false);
        $code = $this->getApplication()->run(new StringInput($command), $output);

        if ($code && $exitOnError) {
            exit($code);
        }

        return $code;
    }

    /**
     * @param $env
     *
     * @return false|mixed
     */
    protected function _getDB(string $env = 'default')
    {
        $dbConf = false;
        if (file_exists(self::ENV_PATH)) {
            $conf = include self::ENV_PATH;
            if (!empty($conf['db']['connection'][$env])) {
                $dbConf = $conf['db']['connection'][$env];
            }

            if (!$dbConf && !empty($conf['db']['connection']['default'])) {
                $dbConf = $conf['db']['connection']['default'];
            }
        }

        if (!$dbConf) {
            return false;
        }

        if (strpos($dbConf['host'], ':')) {
            $tmp = explode(':', $dbConf['host']);
            $dbConf['host'] = $tmp[0];
            $dbConf['port'] = (int) $tmp[1];
        } else {
            $dbConf['port'] = 3306;
        }

        return $dbConf;
    }

    protected function _getEnv($input)
    {

        if ($input->hasOption('env')) {
            $this->_env = $input->getOption('env');
        }

        if (!$this->_env) {
            $this->_env = getenv('ENV');
        }

        if (!$this->_env) {
            $this->_env = 'dev';
        }

        if ($input->hasOption('env')) {
            $input->setOption('env', $this->_env);
        }

        return $this->_env;
    }
}
