<?php

namespace App\Tests\Controller;

use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AlbumControllerTest extends WebTestCase
{

    #[TestWith([1, 'S16 - 01:40:18', 'album'])]
    #[TestWith([66, 'Fuga possimus. - 01:45:39', 'EP'])]
    #[TestWith([80, 'Voluptates molestias. - 01:12:03', 'single'])]
    #[TestWith([109, 'Oui oui - 00:00:00', 'single'])]
    public function testAccessOK(int $albumId, string $title, string $type): void
    {
        $client = static::createClient();
        $client->request('GET', '/album/' . $albumId);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1.card-title', $title);
        $this->assertSelectorTextContains('span.badge', $type);
    }

}
