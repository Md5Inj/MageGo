<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

use Exception;

class LxcManagement
{
    /**
     * Checks if LXC container is running
     *
     * @param $containerName
     * @return bool
     */
    public function isLxcContainerRunning($containerName): bool
    {
        $isRunning = false;

        // Command to list the containers and their statuses
        $command = "lxc list --format=json";

        // Execute the command and capture the output
        exec($command, $output, $returnCode);

        // Check for command execution success
        if ($returnCode === 0) {
            // Convert output to JSON and decode
            $jsonOutput = implode("\n", $output);
            $containers = json_decode($jsonOutput, true);

            // Iterate over containers to find the specified one
            foreach ($containers as $container) {
                if ($container['name'] === $containerName && $container['status'] === 'Running') {
                    $isRunning = true;
                }
            }
        }

        return $isRunning;
    }

    /**
     * Push a file to LXC container
     *
     * @param $containerName
     * @param $filePath
     * @param $destinationPath
     * @return void
     * @throws Exception
     */
    public function pushFile($containerName, $filePath, $destinationPath): void
    {
        $destinationFilePath = $destinationPath . '/' .  basename($filePath);
        $command = "lxc file push -r $filePath $containerName/$destinationPath";
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            throw new Exception("Failed to push file to LXC container. File path: $filePath. Destination: $destinationFilePath");
        }

        $command = "lxc exec $containerName -- chown -R www-data:www-data $destinationFilePath";
        exec($command, $output, $return_var);
        if ($return_var !== 0) {
            throw new Exception('Failed to set owner to www-data.');
        }
    }

    /**
     * Push directory to LXC container
     *
     * @param $containerName
     * @param $directoryPath
     * @param $destinationPath
     * @return void
     * @throws Exception
     */
    public function pushDirectory($containerName, $directoryPath, $destinationPath): void
    {
        $destinationDirectoryPath = $destinationPath . '/' .  basename($directoryPath);
        $command = "lxc file push -r $directoryPath $containerName/$destinationPath";
        exec($command, $output, $return_var);

        if ($return_var !== 0) {
            throw new Exception("Failed to push files to LXC container. Directory path: $directoryPath. Destination: $destinationPath");
        }

        $command = "lxc exec $containerName -- chown -R www-data:www-data $destinationDirectoryPath";
        exec($command, $output, $return_var);
        if ($return_var !== 0) {
            throw new Exception("Failed to set owner to www-data");
        }
    }
}
