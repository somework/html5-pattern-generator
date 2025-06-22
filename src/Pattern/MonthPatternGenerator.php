<?php

declare(strict_types=1);

namespace Html5PatternGenerator\Pattern;

class MonthPatternGenerator
{
    /**
     * Convert a PHP month format into a regex pattern.
     */
    public static function pattern(string $format = 'Y-m'): string
    {
        $map = [
            'Y' => '\\d{4}',
            'm' => '(?:0[1-9]|1[0-2])',
            '-' => '-',
        ];

        $regex = '';
        $length = strlen($format);
        for ($i = 0; $i < $length; $i++) {
            $ch = $format[$i];
            $regex .= $map[$ch] ?? preg_quote($ch, '/');
        }

        return $regex;
    }
}
