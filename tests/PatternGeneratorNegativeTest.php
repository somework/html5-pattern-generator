<?php

declare(strict_types=1);

use Html5PatternGenerator\PatternGenerator;
use PHPUnit\Framework\TestCase;

class PatternGeneratorNegativeTest extends TestCase
{
    public function testNumericRangeThrowsForMinGreaterThanMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PatternGenerator::numericRange(5, 3);
    }

    public function testNumericRangeThrowsForNegativeBound(): void
    {
        $this->expectException(InvalidArgumentException::class);
        PatternGenerator::numericRange(-1, 3);
    }
}
