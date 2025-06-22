<?php

declare(strict_types=1);

use Html5PatternGenerator\Pattern\MonthPatternGenerator;
use PHPUnit\Framework\TestCase;

class MonthPatternGeneratorTest extends TestCase
{
    public function testMatchesValidMonths(): void
    {
        $regex = '/^' . MonthPatternGenerator::pattern() . '$/';
        $this->assertMatchesRegularExpression($regex, '2024-01');
        $this->assertMatchesRegularExpression($regex, '1999-12');
    }

    public function testRejectsInvalidMonths(): void
    {
        $regex = '/^' . MonthPatternGenerator::pattern() . '$/';
        $this->assertDoesNotMatchRegularExpression($regex, '2024-13');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-00');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-1');
    }

    public function testDifferentFormat(): void
    {
        $regex = '/^' . MonthPatternGenerator::pattern('m/Y') . '$/';
        $this->assertMatchesRegularExpression($regex, '05/2024');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-05');
    }
}
