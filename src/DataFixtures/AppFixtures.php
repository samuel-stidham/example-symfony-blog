<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create a demo user
        $user = new User();
        $user->setEmail('demo@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(password_hash('demo_password', PASSWORD_BCRYPT));
        $manager->persist($user);

        $manager->flush();
    }
}
