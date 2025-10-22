<?php

declare(strict_types=1);

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class DatabaseTestCase extends WebTestCase
{
    protected ?EntityManagerInterface $em = null;
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();

        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        // Recreate schema on each test run
        $meta = $this->em->getMetadataFactory()->getAllMetadata();
        if (!$meta) {
            self::fail('No Doctrine metadata found. Check doctrine.yaml mappings and test base class.');
        }
        (new SchemaTool($this->em))->dropDatabase();
        (new SchemaTool($this->em))->createSchema($meta);
    }
}
