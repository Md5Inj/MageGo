<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy;

use MaksimRamashka\Deploy\Console\Console;

require 'autoload.php';

$console = new Console();
$console->execute($argv);

__HALT_COMPILER();
