<?php

declare(strict_types=1);

namespace Html5PatternGenerator\Pattern;

class ColorPatternGenerator
{
    /**
     * Generate a regex pattern matching #RGB or #RRGGBB color values.
     */
    public static function pattern(): string
    {
        return '#(?:[A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})';
    }
}
