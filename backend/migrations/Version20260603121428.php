<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260603121428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_student (user_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_EF2EB139A76ED395 (user_id), INDEX IDX_EF2EB139CB944F1A (student_id), PRIMARY KEY (user_id, student_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_student ADD CONSTRAINT FK_EF2EB139A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_student ADD CONSTRAINT FK_EF2EB139CB944F1A FOREIGN KEY (student_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_student DROP FOREIGN KEY FK_EF2EB139A76ED395');
        $this->addSql('ALTER TABLE user_student DROP FOREIGN KEY FK_EF2EB139CB944F1A');
        $this->addSql('DROP TABLE user_student');
    }
}
