<?php

namespace App\Tests\Controller;

use App\Controller\WordImportController;
use App\Service\WordImportService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use PHPUnit\Framework\TestCase;

class WordImportControllerUnitTest extends TestCase
{
    public function testImportNotAllowed(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $importService = $this->createMock(WordImportService::class);
        $importAllowed = false;
        $wordbaseUrl = uniqid();

        $controller = new WordImportController(
            $entityManager,
            $importService,
            $importAllowed,
            $wordbaseUrl
        );

        $response = $controller->import();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Import not allowed in this environment', $data['error']);
    }

    public function testImportNoFile(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $importService = $this->createMock(WordImportService::class);
        $importAllowed = true;
        $wordbaseUrl = uniqid();

        $controller = new WordImportController(
            $entityManager,
            $importService,
            $importAllowed,
            $wordbaseUrl
        );

        $response = $controller->import();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Failed to download wordbase file', $data['error']);
    }

    public function testImportTmpFileError(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $wordRepository = $this->createMock(EntityRepository::class);
        $importService = $this->createMock(WordImportService::class);
        $importAllowed = true;

        $entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($wordRepository);

        $wordRepository
            ->expects($this->once())
            ->method('count')
            ->willReturn(0);

        $importService
            ->expects($this->once())
            ->method('importFromFile')
            ->willThrowException(new \RuntimeException('Simulated import failure'));

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_wordbase_');
        file_put_contents($tmpFile, 'dummy content');

        $controller = new WordImportController(
            $entityManager,
            $importService,
            $importAllowed,
            $tmpFile
        );

        $response = $controller->import();
        
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(500, $response->getStatusCode());

        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Failed to process tmp file', $data['error']);
    }
}
