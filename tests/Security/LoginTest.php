<?php

declare(strict_types=1);

namespace App\Tests\Security;

use App\Tests\DatabaseTestCase;
use App\Tests\Helper\EntityFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class LoginTest extends DatabaseTestCase
{
    public function testLoginWithValidCredentials(): void
    {
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        EntityFactory::user($this->em, $hasher, 'demo@example.com', 'demo_password');

        $crawler = $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Sign in')->form([
            'email'    => 'demo@example.com',
            'password' => 'demo_password',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString(
            'Logout',
            (string) $this->client->getResponse()->getContent()
        );
    }
}
