<?php

namespace App\Tests\Controller;

use App\Controller\WordImportController;
use App\Service\WordImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class WordImportControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $container = static::getContainer();
        /** @var EntityManagerInterface $em */
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeQuery($platform->getTruncateTableSQL('word', true));
    }

    public function testImport(): void
    {
        $this->client->request('GET', '/api/import-wordbase');

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('rows', $responseData);
        $this->assertEquals(46, $responseData['rows']);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('import_successful', $responseData['status']);
    }

    public function testAlreadyImported(): void
    {
        $this->client->request('GET', '/api/import-wordbase');
        $this->client->request('GET', '/api/import-wordbase');

        $this->assertResponseIsSuccessful();

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('rows', $responseData);
        $this->assertEquals(46, $responseData['rows']);
        $this->assertArrayHasKey('status', $responseData);
        $this->assertEquals('already_imported', $responseData['status']);
    }
}
