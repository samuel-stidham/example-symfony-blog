<?php

declare(strict_types=1);

namespace App\Tests\Routing;

use App\Tests\DatabaseTestCase;
use App\Tests\Helper\EntityFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class SlugRoutingTest extends DatabaseTestCase
{
    public function testIdAndSlugRoutesCoexist(): void
    {
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $user = EntityFactory::user($this->em, $hasher);
        $post = EntityFactory::post($this->em, $user, 'Slug Route Test', 'Body');

        $this->client->request('GET', '/post/' . $post->getId());
        self::assertResponseIsSuccessful();

        $this->client->request('GET', '/post/' . $post->getSlug());
        self::assertResponseIsSuccessful();
    }
}
