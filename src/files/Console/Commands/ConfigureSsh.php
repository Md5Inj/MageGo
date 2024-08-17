<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use Exception;
use MaksimRamashka\Deploy\Model\FileManagement;
use MaksimRamashka\Deploy\Model\LxcManagement;
use MaksimRamashka\Deploy\Model\Shell;
use Phar;

class ConfigureSsh extends AbstractCommand
{
    private const SCRIPT_NAME = 'configure_ssh.sh';

    /**
     * @var string
     */
    protected string $commandName = 'configure:ssh';

    /**
     * @var Phar
     */
    private Phar $phar;

    /**
     * @var LxcManagement
     */
    private LxcManagement $lxcManagement;

    /**
     * @var FileManagement
     */
    private FileManagement $fileManagement;

    /**
     * @var Shell
     */
    private Shell $shell;

    public function __construct()
    {
        $this->shell = new Shell();
        $this->lxcManagement = new LxcManagement();
        $this->fileManagement = new FileManagement();
        $this->phar = new Phar(basename(Phar::running(false)));
    }

    /**
     * Install coding standards
     *
     * @param array $arguments
     * @return void
     * @throws Exception
     */
    public function execute(array $arguments = []): void
    {
        $arguments = $this->parseArguments($arguments);
        if (!isset($arguments['b'])) {
            throw new Exception('Required argument b not passed');
        }

        $containerName = $arguments['b'];
        if (!$this->lxcManagement->isLxcContainerRunning($containerName)) {
            throw new \Exception("LXC container $containerName is not running");
        }

        $sshUser = $arguments['u'] ?? '';
        $keyPath = $arguments['keyPath'] ?? '';

        $scriptPath = 'scripts/' . self::SCRIPT_NAME;

        if ($this->phar->offsetExists($scriptPath)) {
            $scriptContent = $this->phar->offsetGet($scriptPath)->getContent();
            $tempFile = tempnam(sys_get_temp_dir(), 'deploy');
            file_put_contents($tempFile, $scriptContent);
            chmod($tempFile, 0755);
            $this->shell->run($tempFile . ' ' . $containerName . ' ' . $sshUser . ' ' . $keyPath);
            $this->fileManagement->deleteFile($tempFile);
        }
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "
        
configure:ssh: Makes possible to login via SSH to box. Adds private key to the box
    Usage:
        - configure:ssh -b <container_name>
    Parameters:
        -b: The name of the LXC container.
        -u: Name of the SSH user (default = www-data)
        -keyPath: Private key path
";
    }
}
