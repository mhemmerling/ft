<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\Message\Infrastructure\MessageRepository;
use App\User\Domain\User;
use App\User\Infrastructure\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    public function testMainPageIsActive(): void
    {
        $this->client = static::createClient();
        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Main Page');
    }
}
