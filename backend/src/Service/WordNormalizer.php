<?php

namespace App\Service;

class WordNormalizer
{
    public function normalize(string $word): string
    {
        $word = mb_strtolower($word);
        $letters = mb_str_split($word);
        sort($letters);
        return implode('', $letters);
    }
}
