<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

class Shell
{
    /**
     * Run shell command
     *
     * @param string $command
     * @return void
     */
    public function run(string $command): void
    {
        $descriptors = [
            0 => ["pipe", "r"],  // stdin
            1 => ["pipe", "w"],  // stdout
            2 => ["pipe", "w"]   // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);
        if (is_resource($process)) {
            // Close stdin since we're not sending any input
            fclose($pipes[0]);

            // Capture stdout and stderr in real-time
            while (!feof($pipes[1]) || !feof($pipes[2])) {
                if ($output = fgets($pipes[1])) {
                    echo $output;
                }
                if ($error = fgets($pipes[2])) {
                    echo $error;
                }
                flush();
            }

            // Close the pipes
            fclose($pipes[1]);
            fclose($pipes[2]);

            // Close the process and get the return value
            proc_close($process);
        } else {
            echo "Failed to start the process\n";
        }
    }
}
