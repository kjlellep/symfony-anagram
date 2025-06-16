<?php

namespace App\Tests\Controller;

use App\Service\WordImportService;
use App\Service\WordNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class WordImportServiceUnitTest extends TestCase
{
    public function testImportNoFile(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $wordNormalizer = $this->createMock(WordNormalizer::class);
        $filePath = uniqid();

        $controller = new WordImportService(
            $entityManager,
            $wordNormalizer
        );

        $this->expectExceptionMessage('Failed to open wordbase file');
        $controller->importFromFile($filePath);
    }
}
