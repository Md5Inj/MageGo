<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

class MagentoChecker
{
    /**
     * @var string
     */
    private string $directory;

    /**
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Check if a script is running in magento directory
     *
     * @return bool
     */
    public function isMagentoRoot(): bool
    {
        $requiredFilesAndDirs = [
            'app/etc/env.php',  // Environment configuration file
            'bin/magento',      // Magento CLI script
            'app',              // Application code directory
            'lib',              // Library directory
            'vendor',           // Composer dependencies directory
        ];

        foreach ($requiredFilesAndDirs as $path) {
            if (!file_exists($this->directory . '/' . $path)) {
                return false;
            }
        }

        return true;
    }
}
