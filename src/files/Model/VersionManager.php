<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

use Exception;

class VersionManager
{
    private const VERSION = '1.0.1';

    /**
     * @var ReleaseManager
     */
    private ReleaseManager $releaseManager;

    public function __construct()
    {
        $this->releaseManager = new ReleaseManager();
    }

    /**
     * Return version
     *
     * @return string
     */
    public function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Check if a version is latest
     *
     * @return bool
     * @throws Exception
     */
    public function isVersionLatest(): bool
    {
        $latest = true;

        $latestVersion = $this->releaseManager->getLatestVersion();
        if ($latestVersion !== $this->getVersion()) {
            $latest = false;
        }

        return $latest;
    }
}
