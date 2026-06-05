<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260604173247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, activity_type VARCHAR(100) NOT NULL, activity_title VARCHAR(100) NOT NULL, activity_description LONGTEXT DEFAULT NULL, activity_date DATETIME NOT NULL, course_id INT DEFAULT NULL, progress_id INT DEFAULT NULL, INDEX IDX_AC74095A591CC992 (course_id), INDEX IDX_AC74095A43DB87C9 (progress_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE attachment (id INT AUTO_INCREMENT NOT NULL, attachment_name VARCHAR(255) NOT NULL, attachment_path VARCHAR(255) NOT NULL, attachment_type VARCHAR(100) NOT NULL, attachment_size INT NOT NULL, attachment_created_at DATE NOT NULL, message_id INT DEFAULT NULL, INDEX IDX_795FD9BB537A1329 (message_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE classe (id INT AUTO_INCREMENT NOT NULL, class_name VARCHAR(255) NOT NULL, class_description LONGTEXT DEFAULT NULL, teacher_id INT DEFAULT NULL, INDEX IDX_8F87BF9641807E1D (teacher_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE classe_student (classe_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_CCBACF528F5EA509 (classe_id), INDEX IDX_CCBACF52CB944F1A (student_id), PRIMARY KEY (classe_id, student_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, course_title VARCHAR(100) NOT NULL, course_description LONGTEXT NOT NULL, course_resourcefile VARCHAR(255) DEFAULT NULL, course_video_url VARCHAR(255) DEFAULT NULL, classe_id INT DEFAULT NULL, INDEX IDX_169E6FB98F5EA509 (classe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, message_content LONGTEXT NOT NULL, message_sent DATE NOT NULL, sender_id INT DEFAULT NULL, receiver_id INT DEFAULT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FCD53EDB6 (receiver_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, notification_message LONGTEXT NOT NULL, notification_date DATE NOT NULL, notification_lu TINYINT NOT NULL, user_id INT DEFAULT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE progress (id INT AUTO_INCREMENT NOT NULL, progress_percent DOUBLE PRECISION NOT NULL, progress_grade VARCHAR(255) DEFAULT NULL, student_id INT DEFAULT NULL, course_id INT DEFAULT NULL, INDEX IDX_2201F246CB944F1A (student_id), INDEX IDX_2201F246591CC992 (course_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE report (id INT AUTO_INCREMENT NOT NULL, report_type VARCHAR(255) NOT NULL, report_data LONGTEXT NOT NULL, report_generated_at DATETIME NOT NULL, classe_id INT DEFAULT NULL, course_id INT DEFAULT NULL, generated_by_id INT DEFAULT NULL, INDEX IDX_C42F77848F5EA509 (classe_id), INDEX IDX_C42F7784591CC992 (course_id), INDEX IDX_C42F77841BDD81B (generated_by_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, last_name VARCHAR(50) NOT NULL, first_name VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL, type VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, enrollment_date DATE DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE student_classe (student_id INT NOT NULL, classe_id INT NOT NULL, INDEX IDX_1B16716CB944F1A (student_id), INDEX IDX_1B167168F5EA509 (classe_id), PRIMARY KEY (student_id, classe_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE parents_student (parents_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_38639328B706B6D3 (parents_id), INDEX IDX_38639328CB944F1A (student_id), PRIMARY KEY (parents_id, student_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A43DB87C9 FOREIGN KEY (progress_id) REFERENCES progress (id)');
        $this->addSql('ALTER TABLE attachment ADD CONSTRAINT FK_795FD9BB537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE classe ADD CONSTRAINT FK_8F87BF9641807E1D FOREIGN KEY (teacher_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE classe_student ADD CONSTRAINT FK_CCBACF528F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE classe_student ADD CONSTRAINT FK_CCBACF52CB944F1A FOREIGN KEY (student_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB98F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246CB944F1A FOREIGN KEY (student_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE progress ADD CONSTRAINT FK_2201F246591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77848F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F77841BDD81B FOREIGN KEY (generated_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE student_classe ADD CONSTRAINT FK_1B16716CB944F1A FOREIGN KEY (student_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_classe ADD CONSTRAINT FK_1B167168F5EA509 FOREIGN KEY (classe_id) REFERENCES classe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_student ADD CONSTRAINT FK_38639328B706B6D3 FOREIGN KEY (parents_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_student ADD CONSTRAINT FK_38639328CB944F1A FOREIGN KEY (student_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A591CC992');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A43DB87C9');
        $this->addSql('ALTER TABLE attachment DROP FOREIGN KEY FK_795FD9BB537A1329');
        $this->addSql('ALTER TABLE classe DROP FOREIGN KEY FK_8F87BF9641807E1D');
        $this->addSql('ALTER TABLE classe_student DROP FOREIGN KEY FK_CCBACF528F5EA509');
        $this->addSql('ALTER TABLE classe_student DROP FOREIGN KEY FK_CCBACF52CB944F1A');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB98F5EA509');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FCD53EDB6');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F246CB944F1A');
        $this->addSql('ALTER TABLE progress DROP FOREIGN KEY FK_2201F246591CC992');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77848F5EA509');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784591CC992');
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F77841BDD81B');
        $this->addSql('ALTER TABLE student_classe DROP FOREIGN KEY FK_1B16716CB944F1A');
        $this->addSql('ALTER TABLE student_classe DROP FOREIGN KEY FK_1B167168F5EA509');
        $this->addSql('ALTER TABLE parents_student DROP FOREIGN KEY FK_38639328B706B6D3');
        $this->addSql('ALTER TABLE parents_student DROP FOREIGN KEY FK_38639328CB944F1A');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('DROP TABLE classe');
        $this->addSql('DROP TABLE classe_student');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE progress');
        $this->addSql('DROP TABLE report');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE student_classe');
        $this->addSql('DROP TABLE parents_student');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
