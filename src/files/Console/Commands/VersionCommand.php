<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

use MaksimRamashka\Deploy\Model\Output;
use MaksimRamashka\Deploy\Model\VersionManager;

class VersionCommand extends AbstractCommand
{
    protected string $commandName = '--version';

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
        $this->versionManager = new VersionManager();
        $this->output = new Output();
    }

    /**
     * @inheritDoc
     */
    public function execute(array $arguments = []): void
    {
        $this->output->writeInfo($this->versionManager->getVersion());
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "--version: Display the version of the application\n";
    }
}
