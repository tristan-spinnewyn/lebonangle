<?php


namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


class AdvertTest extends ApiTestCase
{
    public function testGoCollection(): void
    {
        $reponse = static::createClient()->request('GET','/api/adverts');

        try {
            self::assertEquals($reponse->getStatusCode(), 200);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function testGetAnInexistingAdvert(): void{
        $reponse = static::createClient()->request('GET','/api/adverts/666');

        try {
            self::assertEquals($reponse->getStatusCode(), 404);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function testGetExistingAdvert(): void{
        $reponse = static::createClient()->request('GET','/api/adverts/1');

        try {
            self::assertEquals($reponse->getStatusCode(), 200);
        } catch (TransportExceptionInterface $e) {
        }
    }

    public function testPostAdvert(): void{
        try {
            $reponse = static::createClient()->request('POST', '/api/adverts', ['json' => [
                'author' => "Test",
                'category' => "/api/categories/22",
                'title' => 'Test titre',
                'content' => 'test contenu',
                'email' => 'spinnewyn.tristan@tutanota.com',
                'price' => 300,
                'pictures' => [
                    "/api/pictures/33",
                    "/api/pictures/34"
                ]
            ]]);
        } catch (TransportExceptionInterface $e) {
            var_dump($e);
        }

        try {
            self::assertEquals($reponse->getStatusCode(), 201);
            $this->assertJsonContains([
                '@context' => '/api/contexts/Advert',
                '@type' => 'Advert',
                'title' => 'Test titre',
                'content' => 'test contenu',
                'author' => 'Test',
                'email' => 'spinnewyn.tristan@tutanota.com',
                'category' => '/api/categories/22',
                'price' => 300,
                'state' => 'draft',
                'publishedAt' => NULL,
                'pictures' => [
                    "/api/pictures/33",
                    "/api/pictures/34"
                ]
            ]);
        } catch (TransportExceptionInterface $e) {
        }
    }
}