<?php

declare(strict_types=1);

namespace Html5PatternGenerator\Pattern;

class WeekPatternGenerator
{
    /**
     * Convert a PHP week format into a regex pattern.
     */
    public static function pattern(string $format = 'Y-\\WW'): string
    {
        $map = [
            'Y' => '\\d{4}',
            'W' => '(?:0[1-9]|[1-4][0-9]|5[0-3])',
        ];

        $regex = '';
        $length = strlen($format);
        for ($i = 0; $i < $length; $i++) {
            $ch = $format[$i];
            if ($ch === '\\') {
                $i++;
                if ($i < $length) {
                    $regex .= preg_quote($format[$i], '/');
                }
                continue;
            }
            $regex .= $map[$ch] ?? preg_quote($ch, '/');
        }

        return $regex;
    }
}
