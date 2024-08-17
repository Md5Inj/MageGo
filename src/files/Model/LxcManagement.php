<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

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
}
