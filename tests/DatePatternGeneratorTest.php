<?php

use Html5PatternGenerator\Pattern\DatePatternGenerator;
use PHPUnit\Framework\TestCase;

class DatePatternGeneratorTest extends TestCase
{
    public function testMatchesValidDates(): void
    {
        $regex = '/^' . DatePatternGenerator::pattern() . '$/';
        $this->assertMatchesRegularExpression($regex, '2024-01-01');
        $this->assertMatchesRegularExpression($regex, '1999-12-31');
    }

    public function testRejectsInvalidDates(): void
    {
        $regex = '/^' . DatePatternGenerator::pattern() . '$/';
        $this->assertDoesNotMatchRegularExpression($regex, '2024-13-01');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-00-10');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-01-32');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-02-31');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-04-31');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-1-01');
    }

    public function testDifferentFormat(): void
    {
        $regex = '/^' . DatePatternGenerator::pattern('d.m.Y') . '$/';
        $this->assertMatchesRegularExpression($regex, '01.05.2024');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-05-01');
        $this->assertDoesNotMatchRegularExpression($regex, '31.02.2024');
    }

    public function testBetween(): void
    {
        $start = new DateTimeImmutable('2024-05-01');
        $end = new DateTimeImmutable('2024-05-03');
        $regex = '/^' . DatePatternGenerator::between($start, $end) . '$/';
        $this->assertMatchesRegularExpression($regex, '2024-05-01');
        $this->assertMatchesRegularExpression($regex, '2024-05-02');
        $this->assertMatchesRegularExpression($regex, '2024-05-03');
        $this->assertDoesNotMatchRegularExpression($regex, '2024-05-04');
    }

    public function testAfterAndBefore(): void
    {
        $start = new DateTimeImmutable('2024-06-01');
        $regexAfter = '/^' . DatePatternGenerator::after($start, 2) . '$/';
        $this->assertMatchesRegularExpression($regexAfter, '2024-06-01');
        $this->assertMatchesRegularExpression($regexAfter, '2024-06-02');
        $this->assertMatchesRegularExpression($regexAfter, '2024-06-03');
        $this->assertDoesNotMatchRegularExpression($regexAfter, '2024-06-04');

        $end = new DateTimeImmutable('2024-07-10');
        $regexBefore = '/^' . DatePatternGenerator::before($end, 1) . '$/';
        $this->assertMatchesRegularExpression($regexBefore, '2024-07-10');
        $this->assertMatchesRegularExpression($regexBefore, '2024-07-09');
        $this->assertDoesNotMatchRegularExpression($regexBefore, '2024-07-08');
    }
}
