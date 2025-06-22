<?php

declare(strict_types=1);

use Html5PatternGenerator\Pattern\WeekPatternGenerator;
use PHPUnit\Framework\TestCase;

class WeekPatternGeneratorTest extends TestCase
{
    public function testMatchesValidWeeks(): void
    {
        $regex = '/^' . WeekPatternGenerator::pattern() . '$/';
        $this->assertMatchesRegularExpression($regex, '2024-W01');
        $this->assertMatchesRegularExpression($regex, '2024-W53');
    }

    public function testRejectsInvalidWeeks(): void
    {
        $regex = '/^' . WeekPatternGenerator::pattern() . '$/';
        $this->assertDoesNotMatchRegularExpression($regex, '2024-W00');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-W54');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-W1');
    }
}
