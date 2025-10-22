<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Post;
use App\Tests\DatabaseTestCase;
use App\Tests\Helper\EntityFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class PostRepositoryTest extends DatabaseTestCase
{
    public function testFindBySlug(): void
    {
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $user = EntityFactory::user($this->em, $hasher);
        $post = EntityFactory::post($this->em, $user, 'Repo Slug', 'Text');

        $found = $this->em->getRepository(Post::class)->findOneBy(['slug' => $post->getSlug()]);
        $this->assertNotNull($found);
        $this->assertSame($post->getId(), $found->getId());
    }
}
