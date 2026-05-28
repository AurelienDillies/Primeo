<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260528093602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, activity_type VARCHAR(100) NOT NULL, activity_title VARCHAR(100) NOT NULL, activity_description LONGTEXT DEFAULT NULL, activity_date DATETIME NOT NULL, course_id INT DEFAULT NULL, progress_id INT DEFAULT NULL, INDEX IDX_AC74095A591CC992 (course_id), INDEX IDX_AC74095A43DB87C9 (progress_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, class_name VARCHAR(255) NOT NULL, class_description LONGTEXT DEFAULT NULL, teacher_id INT DEFAULT NULL, INDEX IDX_8F87BF9641807E1D (teacher_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE classe_user (classe_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_9380A3AF8F5EA509 (classe_id), INDEX IDX_9380A3AFA76ED395 (user_id), PRIMARY KEY (classe_id, user_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, course_title VARCHAR(100) NOT NULL, course_description LONGTEXT NOT NULL, course_resourcefile VARCHAR(255) DEFAULT NULL, course_video_url VARCHAR(255) DEFAULT NULL, classe_id INT DEFAULT NULL, INDEX IDX_169E6FB98F5EA509 (classe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE progress (id INT AUTO_INCREMENT NOT NULL, progress_percent DOUBLE PRECISION NOT NULL, progress_grade VARCHAR(255) DEFAULT NULL, student_id INT DEFAULT NULL, coucourse_id INT DEFAULT NULL, INDEX IDX_2201F246CB944F1A (student_id), INDEX IDX_2201F24648408989 (coucourse_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, report_type VARCHAR(255) NOT NULL, report_data LONGTEXT NOT NULL, report_generated_at DATETIME NOT NULL, classe_id INT DEFAULT NULL, course_id INT DEFAULT NULL, generated_by_id INT DEFAULT NULL, INDEX IDX_C42F77848F5EA509 (classe_id), INDEX IDX_C42F7784591CC992 (course_id), INDEX IDX_C42F77841BDD81B (generated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_user (user_source INT NOT NULL, user_target INT NOT NULL, INDEX IDX_F7129A803AD8644E (user_source), INDEX IDX_F7129A80233D34C1 (user_target), PRIMARY KEY (user_source, user_target)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A43DB87C9 FOREIGN KEY (progress_id) REFERENCES progress (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9641807E1D FOREIGN KEY (teacher_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE classe_user ADD CONSTRAINT FK_9380A3AF8F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classe_user ADD CONSTRAINT FK_9380A3AFA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB98F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246CB944F1A FOREIGN KEY (student_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F24648408989 FOREIGN KEY (coucourse_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77848F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77841BDD81B FOREIGN KEY (generated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A803AD8644E FOREIGN KEY (user_source) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_user ADD CONSTRAINT FK_F7129A80233D34C1 FOREIGN KEY (user_target) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attachment ADD message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BB537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_795FD9BB537A1329 ON attachment (message_id)');
        $this->addSql('ALTER TABLE message ADD sender_id INT DEFAULT NULL, ADD receiver_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FF624B39D ON message (sender_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FCD53EDB6 ON message (receiver_id)');
        $this->addSql('ALTER TABLE notification ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A591CC992');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A43DB87C9');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9641807E1D');
        $this->addSql('ALTER TABLE classe_user DROP FOREIGN KEY FK_9380A3AF8F5EA509');
        $this->addSql('ALTER TABLE classe_user DROP FOREIGN KEY FK_9380A3AFA76ED395');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB98F5EA509');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F246CB944F1A');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F24648408989');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77848F5EA509');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784591CC992');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77841BDD81B');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A803AD8644E');
        $this->addSql('ALTER TABLE user_user DROP FOREIGN KEY FK_F7129A80233D34C1');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE classe_user');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE progress');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE user_user');
        $this->addSql('ALTER TABLE attachment DROP FOREIGN KEY FK_795FD9BB537A1329');
        $this->addSql('DROP INDEX IDX_795FD9BB537A1329 ON attachment');
        $this->addSql('ALTER TABLE attachment DROP message_id');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('DROP INDEX IDX_B6BD307FF624B39D ON message');
        $this->addSql('DROP INDEX IDX_B6BD307FCD53EDB6 ON message');
        $this->addSql('ALTER TABLE message DROP sender_id, DROP receiver_id');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('DROP INDEX IDX_BF5476CAA76ED395 ON notification');
        $this->addSql('ALTER TABLE notification DROP user_id');
    }
}
