<?php

declare(strict_types=1);

use Html5PatternGenerator\Pattern\TimePatternGenerator;
use PHPUnit\Framework\TestCase;

class TimePatternGeneratorTest extends TestCase
{
    public function testMatchesValidTimes(): void
    {
        $regex = '/^' . TimePatternGenerator::pattern() . '$/';
        $this->assertMatchesRegularExpression($regex, '00:00');
        $this->assertMatchesRegularExpression($regex, '23:59');
    }

    public function testRejectsInvalidTimes(): void
    {
        $regex = '/^' . TimePatternGenerator::pattern() . '$/';
        $this->assertDoesNotMatchRegularExpression($regex, '24:00');
        $this->assertDoesNotMatchRegularExpression($regex, '12:60');
        $this->assertDoesNotMatchRegularExpression($regex, '3:00');
    }

    public function testWithSeconds(): void
    {
        $regex = '/^' . TimePatternGenerator::pattern('H:i:s') . '$/';
        $this->assertMatchesRegularExpression($regex, '12:30:15');
        $this->assertDoesNotMatchRegularExpression($regex, '12:30');
        $this->assertDoesNotMatchRegularExpression($regex, '12:30:60');
    }

    public function testAmPm(): void
    {
        $regex = '/^' . TimePatternGenerator::pattern('h:i A') . '$/';
        $this->assertMatchesRegularExpression($regex, '01:30 PM');
        $this->assertMatchesRegularExpression($regex, '12:00 AM');
        $this->assertDoesNotMatchRegularExpression($regex, '00:30 AM');
        $this->assertDoesNotMatchRegularExpression($regex, '13:15 PM');
    }
}
