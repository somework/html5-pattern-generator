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

        $options = [];
        for ($i = $min; $i <= $max; $i++) {
            $options[] = preg_quote((string) $i, '/');
        }

        return '(?:' . implode('|', $options) . ')';
    }
}
