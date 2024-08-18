<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use Exception;
use MaksimRamashka\Deploy\Model\FileManagement;
use MaksimRamashka\Deploy\Model\LxcManagement;
use MaksimRamashka\Deploy\Model\Shell;
use Phar;

class InstallCodingStandardsCommand extends AbstractCommand
{
    private const SCRIPT_NAME = 'install_coding_standards.sh';

    /**
     * @var string
     */
    protected string $commandName = 'install:coding-standards';

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
        $containerName = $arguments['b'];
        $scriptPath = 'scripts/' . self::SCRIPT_NAME;

        if (!$this->lxcManagement->isLxcContainerRunning($containerName)) {
            throw new \Exception("LXC container $containerName is not running");
        }

        if ($this->phar->offsetExists($scriptPath)) {
            $scriptContent = $this->phar->offsetGet($scriptPath)->getContent();
            $tempFile = tempnam(sys_get_temp_dir(), 'deploy');
            file_put_contents($tempFile, $scriptContent);
            chmod($tempFile, 0755);
            $this->shell->run($tempFile . ' ' . $containerName);
            $this->fileManagement->deleteFile($tempFile);
        }
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "
install:coding-standards: Install coding standards tools into lxc container
    Usage:
        - install:coding-standards -b <container_name>
    Parameters:
        - 'b': The name of the LXC container where Xdebug will be configured.";
    }

    /**
     * @inheritDoc
     */
    public function getRequiredParameters(): array
    {
        return ['b'];
    }
}
