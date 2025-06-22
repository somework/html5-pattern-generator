<?php

declare(strict_types=1);

use Html5PatternGenerator\Pattern\ColorPatternGenerator;
use PHPUnit\Framework\TestCase;

class ColorPatternGeneratorTest extends TestCase
{
    public function testMatchesValidHexColors(): void
    {
        $regex = '/^' . ColorPatternGenerator::pattern() . '$/';
        $this->assertMatchesRegularExpression($regex, '#fff');
        $this->assertMatchesRegularExpression($regex, '#FFFFFF');
        $this->assertMatchesRegularExpression($regex, '#123abc');
    }

    public function testRejectsInvalidColors(): void
    {
        $regex = '/^' . ColorPatternGenerator::pattern() . '$/';
        $this->assertDoesNotMatchRegularExpression($regex, 'fff');
        $this->assertDoesNotMatchRegularExpression($regex, '#ffff');
        $this->assertDoesNotMatchRegularExpression($regex, '#ggg');
    }
}
