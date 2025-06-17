<?php

namespace App\Tests\Controller;

use App\Entity\Word;
use App\Service\WordNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnagramControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);

        $connection = $this->entityManager->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeQuery($platform->getTruncateTableSQL('word', true));

        $normalizer = new WordNormalizer();

        $testWords = [
            'pesur',
            'purse',
            'rüpes',
            'aias'
        ];

        foreach ($testWords as $wordText) {
            $word = new Word();
            $word->setText($wordText);
            $word->setSorted($normalizer->normalize($wordText));
            $this->entityManager->persist($word);
        }
        $this->entityManager->flush();
    }

    public function testMissingParameter(): void
    {
        $this->client->request('GET', '/api/anagram');

        $this->assertResponseStatusCodeSame(400);
    }

    public function testAnagramSearch(): void
    {
        $this->client->request('GET', '/api/anagram?word=super');

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('input', $responseData);
        $this->assertEquals('super', $responseData['input']);
        $this->assertArrayHasKey('anagrams', $responseData);
        $this->assertIsArray($responseData['anagrams']);

        $expectedAnagrams = ['pesur', 'purse'];
        $this->assertEqualsCanonicalizing($expectedAnagrams, $responseData['anagrams']);
    }
}
