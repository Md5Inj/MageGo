<?php

declare(strict_types=1);

namespace MaksimRamashka\Deploy\Model;

class Output
{
    private const COLOR_RESET = "\033[0m";
    private const COLOR_GREEN = "\033[32m";
    private const COLOR_RED = "\033[31m";
    private const COLOR_YELLOW = "\033[33m";

    /**
     * Write info without a color
     *
     * @param string $text
     * @param bool $useLinebreak
     * @return void
     */
    public function writeInfo(string $text, bool $useLinebreak = true): void
    {
        echo $text . ($useLinebreak ? "\n" : '');
    }

    /**
     * Write warning text
     *
     * @param string $text
     * @param bool $useLinebreak
     * @return void
     */
    public function writeWarning(string $text, bool $useLinebreak = true): void
    {
        echo self::COLOR_YELLOW . $text . ($useLinebreak ? "\n" : '') . self::COLOR_RESET;
    }

    /**
     * Write error text
     *
     * @param string $text
     * @param bool $useLinebreak
     * @return void
     */
    public function writeError(string $text, bool $useLinebreak = true): void
    {
        echo self::COLOR_RED . $text . ($useLinebreak ? "\n" : '') . self::COLOR_RESET;
    }

    /**
     * Write success text
     *
     * @param string $text
     * @param bool $useLinebreak
     * @return void
     */
    public function writeSuccess(string $text, bool $useLinebreak = true): void
    {
        echo self::COLOR_GREEN . $text . ($useLinebreak ? "\n" : '') . self::COLOR_RESET;
    }
}
