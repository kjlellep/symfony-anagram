<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnagramControllerTest extends WebTestCase
{
    public function testMissingParameter(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/anagram');

        $this->assertResponseStatusCodeSame(400);
    }

    public function testAnagramSearch(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/anagram?word=test');

        $this->assertResponseIsSuccessful();
        $responseData = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('input', $responseData);
        $this->assertEquals('test', $responseData['input']);
        $this->assertArrayHasKey('anagrams', $responseData);
        $this->assertIsArray($responseData['anagrams']);
    }
}
