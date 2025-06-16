<?php

namespace App\Tests\Service;

use App\Entity\Word;
use App\Service\WordNormalizer;
use App\Service\WordImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WordImportServiceTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private WordImportService $importService;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $normalizer = $container->get(WordNormalizer::class);
        $this->importService = new WordImportService($this->entityManager, $normalizer);

        // Clear word table before each test
        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeQuery($platform->getTruncateTableSQL('word', true));
    }

    public function testImportFromEnvWordlist(): void
    {
        $path = $_ENV['WORDBASE_URL'];

        $this->assertFileExists($path, "Test word list file not found: $path");

        $importedCount = $this->importService->importFromFile($path);

        $this->assertEquals(46, $importedCount);

        $repository = $this->entityManager->getRepository(Word::class);
        $words = $repository->findAll();
        $this->assertCount(46, $words);
    }
}
