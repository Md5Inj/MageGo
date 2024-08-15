<?php

$currentDir = dirname(Phar::running(false));
$pharName = basename(Phar::running(false));

function isMagentoRoot($directory) {
    $requiredFilesAndDirs = [
        'app/etc/env.php',  // Environment configuration file
        'bin/magento',      // Magento CLI script
        'app',              // Application code directory
        'lib',              // Library directory
        'vendor',           // Composer dependencies directory
    ];

    foreach ($requiredFilesAndDirs as $path) {
        if (!file_exists($directory . '/' . $path)) {
            return false;
        }
    }

    return true;
}

if (!isMagentoRoot($currentDir)) {
    echo 'Current directory is not Magento root';
    exit(0);
}

$extractDir = $currentDir . '/deploy_files';
if (!is_dir($extractDir)) {
    mkdir($extractDir, 0755, true);
}

$phar = new Phar($pharName);

foreach (new RecursiveIteratorIterator($phar, RecursiveIteratorIterator::SELF_FIRST) as $file) {
    $relativePath = str_replace('phar://' . $phar->getPath() . '/', '', $file->getPathname());

    // Skip 'index.php'
    if ($file->getFilename() === 'index.php') {
        continue;
    }

    // Handle directories
    if ($file->isDir()) {
        $dirPath = $extractDir . '/' . $relativePath;
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }
    } else {
        // Handle files
        $targetFile = $extractDir . '/' . $relativePath;
        copy($file->getPathname(), $targetFile);
    }
}

$etcPath = $currentDir . '/app/etc';
$devPath = $currentDir . '/dev';

rename($extractDir . '/n98-magerun2.yaml', $etcPath . '/n98-magerun2.yaml');
rename($extractDir . '/man98', $devPath . '/man98');

__HALT_COMPILER();
