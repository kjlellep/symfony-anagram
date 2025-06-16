<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\WordNormalizer;

class WordNormalizerTest extends TestCase
{
    public function testNormalizeSimpleWord(): void
    {
        $normalizer = new WordNormalizer();
        $result = $normalizer->normalize('test');

        $this->assertEquals('estt', $result);
    }

    public function testNormalizeWithUppercase(): void
    {
        $normalizer = new WordNormalizer();
        $result = $normalizer->normalize('Test');

        $this->assertEquals('estt', $result);
    }

    public function testNormalizeNordicLetters(): void
    {
        $normalizer = new WordNormalizer();
        $result = $normalizer->normalize('füüsika');

        $this->assertEquals('afiksüü', $result);
    }
}
