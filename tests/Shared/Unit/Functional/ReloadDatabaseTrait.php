<?php

declare(strict_types=1);

namespace App\Tests\Shared\Unit\Functional;

use Doctrine\ORM\Tools\SchemaTool;

trait ReloadDatabaseTrait
{
    protected function setUp(): void
    {
        $entityManager = parent::getContainer()->get('doctrine')->getManager();

        $schemaTool = new SchemaTool($entityManager);
        $metadata = $entityManager->getMetadataFactory()->getAllMetadata();

        // Drop and recreate schema for all entities
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}
