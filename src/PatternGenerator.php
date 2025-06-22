<?php

declare(strict_types=1);

namespace Html5PatternGenerator;

use InvalidArgumentException;

class PatternGenerator
{
    private bool $allowAlpha;
    private bool $allowDigits;
    private string $additional;
    private int $minLength;
    private ?int $maxLength;

    /**
     * @param array{
     *     alpha?: bool,
     *     digits?: bool,
     *     additional?: string,
     *     min?: int,
     *     max?: int|null
     * } $options
     */
    public function __construct(array $options = [])
    {
        $this->allowAlpha = $options['alpha'] ?? true;
        $this->allowDigits = $options['digits'] ?? true;
        $this->additional = $options['additional'] ?? '';
        $this->minLength = $options['min'] ?? 1;
        $this->maxLength = $options['max'] ?? null;

        if ($this->minLength < 0) {
            throw new InvalidArgumentException('Minimum length must be >= 0');
        }
        if ($this->maxLength !== null && $this->maxLength < $this->minLength) {
            throw new InvalidArgumentException('Maximum length must be >= minimum length');
        }
    }

    /**
     * Generate the regex pattern based on provided options.
     */
    public function generate(): string
    {
        $class = '';
        if ($this->allowAlpha) {
            $class .= 'A-Za-z';
        }
        if ($this->allowDigits) {
            $class .= '0-9';
        }
        if ($this->additional !== '') {
            $class .= preg_quote($this->additional, '/');
        }

        if ($class === '') {
            throw new InvalidArgumentException('Character class can not be empty');
        }

        if ($this->maxLength === null) {
            $quantifier = $this->minLength <= 1
                ? ($this->minLength === 1 ? '+' : '{' . $this->minLength . ',}')
                : '{' . $this->minLength . ',}';
        } elseif ($this->minLength === $this->maxLength) {
            $quantifier = '{' . $this->minLength . '}';
        } else {
            $quantifier = '{' . $this->minLength . ',' . $this->maxLength . '}';
        }

        return '[' . $class . ']' . $quantifier;
    }

    /**
     * Generate a pattern matching numbers between two bounds (inclusive).
     */
    public static function numericRange(int $min, int $max): string
    {
        if ($max < $min) {
            throw new InvalidArgumentException('Max must be greater than or equal to min');
        }

        if ($min === $max) {
            return (string) $min;
        }

        if ($min < 0 || $max < 0) {
            throw new InvalidArgumentException('Only non-negative numbers are supported');
        }

        $patterns = self::splitToPatterns($min, $max);
        $parts = array_map(static fn (array $p) => $p['string'], $patterns);

        $result = implode('|', $parts);

        return count($parts) > 1 ? '(?:' . $result . ')' : $result;
    }

    /**
     * @return array<int, array{pattern:string,count:list<int>,string:string}>
     */
    private static function splitToPatterns(int $min, int $max): array
    {
        $ranges = self::splitToRanges($min, $max);
        $tokens = [];
        $start = $min;
        $prev = null;

        foreach ($ranges as $stop) {
            $obj = self::rangeToPattern((string) $start, (string) $stop);

            if ($prev !== null && $prev['pattern'] === $obj['pattern']) {
                if (count($prev['count']) > 1) {
                    array_pop($prev['count']);
                }
                $prev['count'][] = $obj['count'];
                $prev['string'] = $prev['pattern'] . self::toQuantifier($prev['count']);
                $tokens[array_key_last($tokens)] = $prev;
            } else {
                $token = [
                    'pattern' => $obj['pattern'],
                    'count' => [$obj['count']],
                    'string' => $obj['pattern'] . self::toQuantifier([$obj['count']]),
                ];
                $tokens[] = $token;
                $prev = &$tokens[array_key_last($tokens)];
            }

            $start = $stop + 1;
        }

        return $tokens;
    }

    /**
     * @return list<int>
     */
    private static function splitToRanges(int $min, int $max): array
    {
        $nines = 1;
        $zeros = 1;

        $stop = self::countNines($min, $nines);
        $stops = [$max];

        while ($min <= $stop && $stop <= $max) {
            $stops[] = $stop;
            $nines += 1;
            $stop = self::countNines($min, $nines);
        }

        $stop = self::countZeros($max + 1, $zeros) - 1;

        while ($min < $stop && $stop <= $max) {
            $stops[] = $stop;
            $zeros += 1;
            $stop = self::countZeros($max + 1, $zeros) - 1;
        }

        $stops = array_values(array_unique($stops));
        sort($stops);

        return $stops;
    }

    /**
     * @return array{pattern:string,count:int}
     */
    private static function rangeToPattern(string $start, string $stop): array
    {
        if ($start === $stop) {
            return ['pattern' => preg_quote($start, '/'), 'count' => 0];
        }

        $zipped = self::zip($start, $stop);
        $pattern = '';
        $count = 0;

        foreach ($zipped as [$startDigit, $stopDigit]) {
            if ($startDigit === $stopDigit) {
                $pattern .= preg_quote($startDigit, '/');
            } elseif ($startDigit !== '0' || $stopDigit !== '9') {
                $pattern .= '[' . $startDigit . '-' . $stopDigit . ']';
            } else {
                $count++;
            }
        }

        if ($count > 0) {
            $pattern .= '[0-9]';
        }

        return ['pattern' => $pattern, 'count' => $count];
    }

    /**
     * @return list<array{0:string,1:string}>
     */
    private static function zip(string $a, string $b): array
    {
        $arr = [];
        $len = strlen($a);
        for ($i = 0; $i < $len; $i++) {
            $arr[] = [$a[$i], $b[$i]];
        }
        return $arr;
    }

    private static function countNines(int $min, int $len): int
    {
        $str = (string) $min;
        $prefix = $len < strlen($str) ? substr($str, 0, -$len) : '';
        return (int) ($prefix . str_repeat('9', $len));
    }

    private static function countZeros(int $integer, int $zeros): int
    {
        return $integer - ($integer % (10 ** $zeros));
    }

    /**
     * @param list<int> $digits
     */
    private static function toQuantifier(array $digits): string
    {
        $start = $digits[0] ?? 0;
        $stop = $digits[1] ?? '';

        return ($stop !== '' || $start > 1)
            ? '{' . (string) $start . ($stop !== '' ? ',' . (string) $stop : '') . '}'
            : '';
    }
}
