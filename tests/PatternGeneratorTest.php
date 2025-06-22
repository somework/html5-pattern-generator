<?php

declare(strict_types=1);

use Html5PatternGenerator\PatternGenerator;
use PHPUnit\Framework\TestCase;

class PatternGeneratorTest extends TestCase
{
    public function testGenerateMatchesAlphaNumeric(): void
    {
        $generator = new PatternGenerator();
        $regex = '/^' . $generator->generate() . '$/';
        $this->assertMatchesRegularExpression($regex, 'abc123');
        $this->assertDoesNotMatchRegularExpression($regex, 'abc-123');
    }

    public function testCustomCharacterClassAndLength(): void
    {
        $generator = new PatternGenerator([
            'alpha' => true,
            'digits' => false,
            'additional' => '-_',
            'min' => 2,
            'max' => 4,
        ]);
        $regex = '/^' . $generator->generate() . '$/';
        $this->assertMatchesRegularExpression($regex, 'ab');
        $this->assertMatchesRegularExpression($regex, 'a-b_');
        $this->assertDoesNotMatchRegularExpression($regex, 'a');
        $this->assertDoesNotMatchRegularExpression($regex, 'abcde');
        $this->assertDoesNotMatchRegularExpression($regex, 'ab1');
    }

    public function testNumericRangeHelper(): void
    {
        $regex = '/^' . PatternGenerator::numericRange(1, 3) . '$/';
        $this->assertMatchesRegularExpression($regex, '1');
        $this->assertMatchesRegularExpression($regex, '2');
        $this->assertMatchesRegularExpression($regex, '3');
        $this->assertDoesNotMatchRegularExpression($regex, '4');
    }

    public function testNumericRangeLarge(): void
    {
        $pattern = PatternGenerator::numericRange(1, 1000);
        $regex = '/^' . $pattern . '$/';

        $this->assertMatchesRegularExpression($regex, '1');
        $this->assertMatchesRegularExpression($regex, '10');
        $this->assertMatchesRegularExpression($regex, '999');
        $this->assertMatchesRegularExpression($regex, '1000');

        $this->assertDoesNotMatchRegularExpression($regex, '0');
        $this->assertDoesNotMatchRegularExpression($regex, '1001');

        $this->assertLessThan(100, strlen($pattern));
    }
}
