<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Archived Migration (moved because it failed on fresh DBs)
 */
final class archived_Version20260602075915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ARCHIVED: originally ALTER TABLE user ADD type';
    }

    public function up(Schema $schema): void
    {
        // archived
    }

    public function down(Schema $schema): void
    {
        // archived
    }
}
