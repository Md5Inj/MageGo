<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use Exception;
use MaksimRamashka\Deploy\Model\Output;
use MaksimRamashka\Deploy\Model\ReleaseManager;
use MaksimRamashka\Deploy\Model\VersionManager;

class SelfUpdateCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected string $commandName = 'self-update';

    /**
     * @var Output
     */
    private Output $output;

    /**
     * @var ReleaseManager
     */
    private ReleaseManager $releaseManager;

    /**
     * @var VersionManager
     */
    private VersionManager $versionManager;

    public function __construct()
    {
        $this->output = new Output();
        $this->releaseManager = new ReleaseManager();
        $this->versionManager = new VersionManager();
    }

    /**
     * @inheritDoc
     */
    public function execute(array $arguments = []): void
    {
        if ($this->versionManager->isVersionLatest()) {
            $this->output->writeInfo('Version is already latest');
            return;
        }

        try {
            $this->releaseManager->updateToLatest();
        } catch (Exception $e) {
            $this->output->writeError($e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "self-update: Updates script to the latest version\n";
    }
}
