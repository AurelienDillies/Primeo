<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260604125426 extends AbstractMigration
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
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY `FK_F7129A80233D34C1`');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY `FK_F7129A803AD8644E`');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY `FK_2201F24648408989`');
        $this->addSql('DROP INDEX IDX_2201F24648408989 ON progress');
        $this->addSql('ALTER TABLE progress CHANGE coucourse_id course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_2201F246591CC992 ON progress (course_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A80233D34C1 (user_target), INDEX IDX_F7129A803AD8644E (user_source), PRIMARY KEY (user_source, user_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT `FK_F7129A80233D34C1` FOREIGN KEY (user_target) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT `FK_F7129A803AD8644E` FOREIGN KEY (user_source) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_student DROP FOREIGN KEY FK_EF2EB139A76ED395');
        $this->addSql('ALTER TABLE user_student DROP FOREIGN KEY FK_EF2EB139CB944F1A');
        $this->addSql('DROP TABLE user_student');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F246591CC992');
        $this->addSql('DROP INDEX IDX_2201F246591CC992 ON progress');
        $this->addSql('ALTER TABLE progress CHANGE course_id coucourse_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT `FK_2201F24648408989` FOREIGN KEY (coucourse_id) REFERENCES course (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2201F24648408989 ON progress (coucourse_id)');
    }
}
