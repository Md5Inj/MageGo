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
        'Model',
        'scripts'
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
     * @var LxcManagement
     */
    private LxcManagement $lxcManagement;

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
        $this->lxcManagement = new LxcManagement();
    }

    /**
     * Extract all files for phar
     *
     * @param string $containerName
     * @param string $magentoDirectory
     * @return void
     */
    public function execute(string $containerName, string $magentoDirectory): void
    {
        try {
            $this->extractMageRunFiles();

            $etcPath = $magentoDirectory . '/app/etc';
            $devPath = $magentoDirectory . '/dev';

            $yamlFilePath = $this->extractDir . '/n98-magerun2.yaml';
            $man98DirectoryPath = $this->extractDir . '/man98';

            $this->lxcManagement->pushFile($containerName, $yamlFilePath, $etcPath);
            $this->lxcManagement->pushDirectory($containerName, $man98DirectoryPath, $devPath);
            $this->output->writeSuccess('Files are successfully pushed to Magento.');

            $this->fileManagement->deleteDirectory($this->extractDir);
            $this->output->writeSuccess('Temp files are successfully deleted.');
        } catch (Exception $e) {
            $this->output->writeError('An error occurred while trying to deploy files. Error: ' . $e->getMessage());
        }
    }

    /**
     * Extract MageRun2 files from phar
     *
     * @return void
     * @throws Exception
     */
    public function extractMageRunFiles(): void
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
                || str_contains($file->getPathName(), '/scripts/')
            ) {
                continue;
            }

            if ($file->isDir()) {
                $this->fileManagement->createDirectory($this->extractDir . '/' . $relativePath);
            } else {
                $this->fileManagement->copyFile($file, $this->extractDir . '/' . $relativePath);
            }
        }

        $this->output->writeSuccess('Files are successfully extracted.');
    }
}
