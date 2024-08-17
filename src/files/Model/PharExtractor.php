<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

use Exception;
use Phar;
use RecursiveIteratorIterator;

class PharExtractor
{
    private const SERVICE_FILES_LIST = [
        'index.php',
        'autoload.php',
        'Console',
        'Model'
    ];

    /**
     * @var Phar
     */
    private Phar $phar;

    /**
     * @var FileManagement
     */
    private FileManagement $fileManagement;

    /**
     * @var Output
     */
    private Output $output;

    /**
     * @param string $pharName
     * @param string $extractDir
     */
    public function __construct(
        private readonly string $pharName,
        private readonly string $extractDir
    ) {
        $this->output = new Output();

        if (empty($this->pharName)) {
            $this->output->writeError('Current file is not phar');
            exit(0);
        }

        $this->phar = new Phar($this->pharName);
        $this->fileManagement = new FileManagement();
    }

    /**
     * Extract all files for phar
     *
     * @return void
     * @throws Exception
     */
    public function execute(): void
    {
        if (!is_dir($this->extractDir)) {
            mkdir($this->extractDir, 0755, true);
        }

        // Extract files from phar
        foreach (new RecursiveIteratorIterator($this->phar, RecursiveIteratorIterator::SELF_FIRST) as $file) {
            $relativePath = str_replace('phar://' . $this->phar->getPath() . '/', '', $file->getPathname());

            // Skip service files
            if (
                in_array($file->getFilename(), self::SERVICE_FILES_LIST)
                || str_contains($file->getPathName(), '/Model/')
                || str_contains($file->getPathName(), '/Console/')
            ) {
                continue;
            }

            if ($file->isDir()) {
                $this->fileManagement->createDirectory($this->extractDir . '/' . $relativePath);
            } else {
                $this->fileManagement->copyFile($file, $this->extractDir . '/' . $relativePath);
            }
        }

        $currentDir = dirname(Phar::running(false));
        $etcPath = $currentDir . '/app/etc';
        $devPath = $currentDir . '/dev';

        rename($this->extractDir . '/n98-magerun2.yaml', $etcPath . '/n98-magerun2.yaml');
        rename($this->extractDir . '/man98', $devPath . '/man98');
    }
}
