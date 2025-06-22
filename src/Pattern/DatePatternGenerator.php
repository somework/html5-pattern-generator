<?php

declare(strict_types=1);

namespace Html5PatternGenerator\Pattern;

use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use InvalidArgumentException;

class DatePatternGenerator
{
    /**
     * Convert a PHP date format into a regex pattern.
     */
    public static function pattern(string $format = 'Y-m-d'): string
    {
        if ($format === 'Y-m-d') {
            return '(?:' .
                '\\d{4}-' .
                '(?:'
                    . '(?:0[13578]|1[02])-(?:0[1-9]|[12][0-9]|3[01])|'
                    . '(?:0[469]|11)-(?:0[1-9]|[12][0-9]|30)|'
                    . '02-(?:0[1-9]|1[0-9]|2[0-9])'
                . ')' .
            ')';
        }

        if ($format === 'd.m.Y') {
            return '(?:' .
                '(?:'
                    . '(?:0[1-9]|[12][0-9]|3[01])\\.(?:0[13578]|1[02])|'
                    . '(?:0[1-9]|[12][0-9]|30)\\.(?:0[469]|11)|'
                    . '(?:0[1-9]|1[0-9]|2[0-9])\.02'
                . ')\\.' .
                '\\d{4}' .
            ')';
        }

        $map = [
            'Y' => '\\d{4}',
            'm' => '(0[1-9]|1[0-2])',
            'd' => '(0[1-9]|[12][0-9]|3[01])',
        ];

        $regex = '';
        $length = strlen($format);
        for ($i = 0; $i < $length; $i++) {
            $ch = $format[$i];
            $regex .= $map[$ch] ?? preg_quote($ch, '/');
        }
        return $regex;
    }

    /**
     * Generate a regex pattern for all dates between two boundaries (inclusive).
     */
    public static function between(DateTimeImmutable $from, DateTimeImmutable $to, string $format = 'Y-m-d'): string
    {
        if ($to < $from) {
            throw new InvalidArgumentException('End date must not be earlier than start date');
        }

        $pattern = [];
        $period = new DatePeriod($from, new DateInterval('P1D'), $to->modify('+1 day'));
        foreach ($period as $date) {
            $pattern[] = preg_quote($date->format($format), '/');
        }

        return '(?:' . implode('|', $pattern) . ')';
    }

    /**
     * Generate a regex pattern for dates after the given date (inclusive) for a limited range.
     */
    public static function after(DateTimeImmutable $from, int $days = 365, string $format = 'Y-m-d'): string
    {
        $to = $from->modify('+' . $days . ' days');
        return self::between($from, $to, $format);
    }

    /**
     * Generate a regex pattern for dates before the given date (inclusive) for a limited range.
     */
    public static function before(DateTimeImmutable $to, int $days = 365, string $format = 'Y-m-d'): string
    {
        $from = $to->modify('-' . $days . ' days');
        return self::between($from, $to, $format);
    }
}
