<?php

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
}
