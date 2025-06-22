<?php

declare(strict_types=1);

use Html5PatternGenerator\Pattern\DatePatternGenerator;
use PHPUnit\Framework\TestCase;

class DatePatternGeneratorNegativeTest extends TestCase
{
    public function testBetweenThrowsForInvalidRange(): void
    {
        $this->expectException(InvalidArgumentException::class);
        DatePatternGenerator::between(
            new DateTimeImmutable('2024-05-10'),
            new DateTimeImmutable('2024-05-01')
        );
    }
}
