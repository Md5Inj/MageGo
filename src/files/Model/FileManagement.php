<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

use Exception;

class FileManagement
{
    /**
     * Create directory
     *
     * @param string $fullPath
     * @return void
     */
    public function createDirectory(string $fullPath): void
    {
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
    }

    /**
     * Copy file
     *
     * @param $file
     * @param string $fullPath
     * @return void
     * @throws Exception
     */
    public function copyFile($file, string $fullPath): void
    {
        $this->checkForFileExistance($file->getPathname());
        copy($file->getPathname(), $fullPath);
    }

    /**
     * Move file
     *
     * @param string $oldFilePath
     * @param string $newFilePath
     * @return void
     * @throws Exception
     */
    public function moveFile(string $oldFilePath, string $newFilePath): void
    {
        $this->checkForFileExistance($oldFilePath);
        rename($oldFilePath, $newFilePath);
    }

    /**
     * Check if a file exists
     *
     * @param string $filePath
     * @return bool
     */
    public function fileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

    /**
     * Delete file
     *
     * @param string $filePath
     * @return void
     * @throws Exception
     */
    public function deleteFile(string $filePath): void
    {
        $this->checkForFileExistance($filePath);
        unlink($filePath);
    }

    /**
     * Throw an error if a file not exists
     *
     * @param string $filePath
     * @return void
     * @throws Exception
     */
    private function checkForFileExistance(string $filePath): void
    {
        if (!$this->fileExists($filePath)) {
            throw new Exception("File '$filePath' is not exists");
        }
    }
}
