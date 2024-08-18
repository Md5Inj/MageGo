<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

class MagentoChecker
{
    /**
     * Check if a script is running in magento directory
     *
     * @param string $containerName
     * @param string $folderPath
     * @return bool
     */
    public function isMagentoRoot(string $containerName, string $folderPath): bool
    {
        // List of essential Magento 2 files
        $magentoFiles = [
            'app/etc/config.php',
            'bin/magento',
            'composer.json',
            'pub/index.php'
        ];

        // Prepare the command to check for each file
        $commands = array_map(function($file) use ($folderPath) {
            return "[ -f \"$folderPath/$file\" ]";
        }, $magentoFiles);

        // Combine all the checks with "&&" so that all must be true
        $command = implode(' && ', $commands);

        // Run the command in the LXC container
        $fullCommand = "lxc exec $containerName -- sh -c '$command'";

        // Execute the command and get the return status
        exec($fullCommand, $output, $return_var);

        // Return true if all files exist, false otherwise
        return $return_var === 0;
    }
}
