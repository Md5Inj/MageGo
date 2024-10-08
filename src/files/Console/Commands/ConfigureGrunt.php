<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use Exception;
use MaksimRamashka\Deploy\Model\FileManagement;
use MaksimRamashka\Deploy\Model\LxcManagement;
use MaksimRamashka\Deploy\Model\Shell;
use Phar;

class ConfigureGrunt extends AbstractCommand
{
    private const SCRIPT_NAME = 'configure_grunt.sh';

    /**
     * @var string
     */
    protected string $commandName = 'configure:grunt';

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
        $magentoDirectory = $arguments['magentoDir'] ?? '/var/www/source';
        $scriptPath = 'scripts/' . self::SCRIPT_NAME;

        if (!$this->lxcManagement->isLxcContainerRunning($containerName)) {
            throw new Exception("LXC container $containerName is not running");
        }

        if ($this->phar->offsetExists($scriptPath)) {
            $scriptContent = $this->phar->offsetGet($scriptPath)->getContent();
            $tempFile = tempnam(sys_get_temp_dir(), 'deploy');
            file_put_contents($tempFile, $scriptContent);
            chmod($tempFile, 0755);

            $this->shell->run("lxc file push $tempFile $containerName/$magentoDirectory/" . self::SCRIPT_NAME);
            $this->shell->run( "lxc exec $containerName --env HOME=/var/www --user 33 --group 33 -- bash -c \"cd $magentoDirectory && ./" . self::SCRIPT_NAME . '"');
            $this->shell->run( "lxc exec $containerName --env HOME=/var/www --user 33 --group 33 -- bash -c \"rm $magentoDirectory/" . self::SCRIPT_NAME . '"');
            $this->fileManagement->deleteFile($tempFile);
        }
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "
configure:grunt: Make all stuff to install Grunt including local-themes file creation. After execution grunt already can be used
    Usage:
        - configure:ssh -b <container_name>
    Parameters:
        -b: The name of the LXC container
        -magentoDir: Magento root directory (default = /var/www/source)
";
    }

    /**
     * @inheritDoc
     */
    public function getRequiredParameters(): array
    {
        return ['b'];
    }
}
