<?php

namespace App\Controller;

use App\Entity\Word;
use App\Service\WordNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnagramController extends AbstractController
{
    public function findAnagram(
        Request $request,
        EntityManagerInterface $entityManager,
        WordNormalizer $wordNormalizer
    ): JsonResponse {
        $inputWord = $request->query->get('word');

        if (!$inputWord) {
            return new JsonResponse(['error' => 'Missing word parameter'], 400);
        }

        $sorted = $wordNormalizer->normalize($inputWord);

        $repo = $entityManager->getRepository(Word::class);
        $results = $repo->findBy(['sorted' => $sorted]);

        $anagrams = array_map(fn($word) => $word->getText(), $results);

        return new JsonResponse([
            'input' => $inputWord,
            'anagrams' => $anagrams
        ]);
    }
}
