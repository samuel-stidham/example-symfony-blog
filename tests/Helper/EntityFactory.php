<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class EntityFactory
{
    public static function user(EntityManagerInterface $em, UserPasswordHasherInterface $hasher, string $email = 'demo@example.com', string $plain = 'demo_password'): User
    {
        $u = new User();
        $u->setEmail($email)->setRoles(['ROLE_USER']);
        $u->setPassword($hasher->hashPassword($u, $plain));
        $em->persist($u);
        $em->flush();
        return $u;
    }

    public static function post(EntityManagerInterface $em, User $author, string $title = 'Hello', string $content = 'World'): Post
    {
        $p = (new Post())->setTitle($title)->setContent($content)->setAuthor($author);
        $em->persist($p);
        $em->flush();
        return $p;
    }
}
