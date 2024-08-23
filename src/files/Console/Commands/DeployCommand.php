<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use Exception;
use MaksimRamashka\Deploy\Model\LxcManagement;
use MaksimRamashka\Deploy\Model\MagentoChecker;
use MaksimRamashka\Deploy\Model\Output;
use MaksimRamashka\Deploy\Model\PharExtractor;
use Phar;

class DeployCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected string $commandName = 'deploy';

    /**
     * @var Output
     */
    private Output $output;

    /**
     * @var LxcManagement
     */
    private LxcManagement $lxcManagement;

    /**
     * @var MagentoChecker
     */
    private MagentoChecker $magentoChecker;

    public function __construct()
    {
        $this->output = new Output();
        $this->lxcManagement = new LxcManagement();
        $this->magentoChecker = new MagentoChecker();
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function execute(array $arguments = []): void
    {
        $containerName = $arguments['b'];
        $magentoDirectory = $arguments['magentoDir'] ?? '/var/www/source';

        if (!$this->lxcManagement->isLxcContainerRunning($containerName)) {
            throw new Exception("LXC container $containerName is not running");
        }

        $pharName = basename(Phar::running(false));
        $extractDir = '/tmp/deploy_files';

        if (empty($pharName)) {
            $this->output->writeError('Something went wrong when initializing script');
            return;
        }

        if (!$this->magentoChecker->isMagentoRoot($containerName, $magentoDirectory)) {
            $this->output->writeError('Current directory is not Magento root');
            exit(0);
        }

        $pharExtractor = new PharExtractor($pharName, $extractDir);
        $pharExtractor->execute($containerName, $magentoDirectory);
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "
deploy: Deploy MageRun2 custom commands to Magento
    Usage:
        - deploy -b <container_name> -magentoDir <Magento directory>
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
