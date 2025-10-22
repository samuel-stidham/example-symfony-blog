<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\DatabaseTestCase;
use App\Tests\Helper\EntityFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class HomeControllerTest extends DatabaseTestCase
{
    public function testHomeLoadsAndShowsPosts(): void
    {
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $user = EntityFactory::user($this->em, $hasher);
        EntityFactory::post($this->em, $user, 'First Post', 'Content here');

        $this->client->request('GET', '/');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h2', 'Latest Posts');
        self::assertSelectorTextContains('article a', 'First Post');
    }
}
