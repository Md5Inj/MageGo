<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

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

    public function __construct()
    {
        $this->output = new Output();
    }

    /**
     * @inheritDoc
     */
    public function execute(array $arguments = []): void
    {
        // @TDOO re-check how extract goes

        $currentDir = dirname(Phar::running(false));
        $pharName = basename(Phar::running(false));
        $extractDir = $currentDir . '/deploy_files';

        if (empty($currentDir) || empty($pharName)) {
            $this->output->writeError('Something went wrong when initializing script');
            exit(0);
        }

        $magentoChecker = new MagentoChecker($currentDir);
        if (!$magentoChecker->isMagentoRoot()) {
            $this->output->writeError('Current directory is not Magento root');
            exit(0);
        }

        $pharExtractor = new PharExtractor($pharName, $extractDir);
        $pharExtractor->execute();
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "deploy: Deploy mr2 custom commands to Magento\n";
    }
}
