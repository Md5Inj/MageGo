<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use Exception;
use MaksimRamashka\Deploy\Model\Output;
use MaksimRamashka\Deploy\Model\ReleaseManager;
use MaksimRamashka\Deploy\Model\VersionManager;

class OutdatedCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected string $commandName = 'outdated';

    /**
     * @var ReleaseManager
     */
    private ReleaseManager $releaseManager;

    /**
     * @var VersionManager
     */
    private VersionManager $versionManager;

    /**
     * @var Output
     */
    private Output $output;

    public function __construct()
    {
        $this->releaseManager = new ReleaseManager();
        $this->versionManager = new VersionManager();
        $this->output = new Output();
    }

    /**
     * Check a script for updates
     *
     * @param array $arguments
     * @return void
     * @throws Exception
     */
    public function execute(array $arguments = []): void
    {
        $latestVersion = $this->releaseManager->getLatestVersion();
        $currentVersion = $this->versionManager->getVersion();

        if (empty($latestVersion)) {
            $this->output->writeError('Failed to fetch the latest version.');
            return;
        }

        if (empty($currentVersion)) {
            $this->output->writeError('Current version is unknown.');
            return;
        }

        if ($latestVersion === $currentVersion) {
            if (($arguments['print-only-outdated'] ?? false) === true) {
                return;
            }

            $this->output->writeSuccess('The PHAR file is up-to-date.');
            $this->output->writeInfo("Current version: "); $this->output->writeSuccess($currentVersion);
        } else {
            $this->output->writeInfo('The PHAR file is outdated.');
            $this->output->writeInfo('Latest version: ', false); $this->output->writeError($latestVersion);
            $this->output->writeInfo("Current version: $currentVersion");
            $this->output->writeInfo('Please use argument self-update to update script');
        }
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "outdated: Checks script for updates\n";
    }
}
