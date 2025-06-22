<?php

declare(strict_types=1);

namespace Html5PatternGenerator\Pattern;

class TimePatternGenerator
{
    /**
     * Convert a PHP time format into a regex pattern.
     */
    public static function pattern(string $format = 'H:i'): string
    {
        $map = [
            'H' => '(?:[01][0-9]|2[0-3])',
            'h' => '(?:0[1-9]|1[0-2])',
            'i' => '[0-5][0-9]',
            's' => '[0-5][0-9]',
            'A' => '(?:AM|PM)',
            ':' => ':',
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
