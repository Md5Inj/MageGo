<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Console\Commands;

class VerboseCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected string $commandName = '-vvv';

    /**
     * @inheritDoc
     */
    public function execute(array $arguments = []): void
    {
        // @TODO implement this
        ini_set('display_errors', '1');
        error_reporting(E_ALL);
    }

    /**
     * @inheritDoc
     */
    public function getHelpText(): string
    {
        return "-vvv: Show verbose information if something is wrong\n";
    }
}
