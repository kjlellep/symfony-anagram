<?php

namespace App\Service;

use App\Entity\Word;
use Doctrine\ORM\EntityManagerInterface;

class WordImportService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly WordNormalizer $normalizer
    ) {
    }

    public function importFromFile(string $filePath): int
    {
        $handle = @fopen($filePath, 'r');
        if (!$handle) {
            throw new \RuntimeException("Failed to open wordbase file");
        }

        $imported = 0;

        while (($line = fgets($handle)) !== false) {
            $word = mb_convert_encoding(trim($line), 'UTF-8', 'ISO-8859-1, UTF-8');

            if ($word === '') {
                continue;
            }

            $sorted = $this->normalizer->normalize($word);

            $entity = new Word();
            $entity->setText($word);
            $entity->setSorted($sorted);

            $this->entityManager->persist($entity);
            $imported++;

            if ($imported % 1000 === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }

        fclose($handle);
        $this->entityManager->flush();

        return $imported;
    }
}
