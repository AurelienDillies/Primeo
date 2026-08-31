<?php

use App\DataFixtures\AppFixtures;
use App\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if (($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? 'dev') === 'test') {
    $kernel = new App\Kernel('test', true);
    $kernel->boot();

    $entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    $schemaManager = $entityManager->getConnection()->createSchemaManager();
    $existingTables = array_map('strtolower', $schemaManager->listTableNames());
    $requiredTables = array_map(
        static fn ($metadata) => strtolower($metadata->getTableName()),
        $entityManager->getMetadataFactory()->getAllMetadata()
    );

    if (array_diff($requiredTables, $existingTables) !== []) {
        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    if ($entityManager->getRepository(User::class)->count([]) === 0) {
        (new AppFixtures())->load($entityManager);
    }
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
