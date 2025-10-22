<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class DatabaseTestCase extends WebTestCase
{
    protected EntityManagerInterface $em;
    protected KernelBrowser $client;
    protected UserPasswordHasherInterface $hasher;

    protected function setUp(): void
    {
        parent::setUp();

        // Always start fresh
        self::ensureKernelShutdown();
        $this->client = static::createClient();

        $c = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $c->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $c->get(UserPasswordHasherInterface::class);

        $this->em = $em;
        $this->hasher = $hasher;

        // Recreate schema each test
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        if ($metadata === []) {
            self::fail('No Doctrine metadata found. Check doctrine mappings.');
        }

        $tool = new SchemaTool($this->em);
        $tool->dropDatabase();
        $tool->createSchema($metadata);
    }

    protected function tearDown(): void
    {
        // Properly close EM between tests to avoid memory leaks
        if (isset($this->em)) {
            $this->em->clear();
        }
        parent::tearDown();
    }
}
