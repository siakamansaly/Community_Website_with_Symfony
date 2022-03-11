<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220309153540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment ADD user_id INT NOT NULL, ADD trick_id INT NOT NULL');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_9474526CA76ED395 ON comment (user_id)');
        $this->addSql('CREATE INDEX IDX_9474526CB281BE2E ON comment (trick_id)');
        $this->addSql('ALTER TABLE media ADD trick_id INT NOT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CB281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10CB281BE2E ON media (trick_id)');
        $this->addSql('ALTER TABLE trick ADD user_id INT NOT NULL, ADD type_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EC54C8C93 FOREIGN KEY (type_id) REFERENCES type_trick (id)');
        $this->addSql('CREATE INDEX IDX_D8F0A91EA76ED395 ON trick (user_id)');
        $this->addSql('CREATE INDEX IDX_D8F0A91EC54C8C93 ON trick (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CB281BE2E');
        $this->addSql('DROP INDEX IDX_9474526CA76ED395 ON comment');
        $this->addSql('DROP INDEX IDX_9474526CB281BE2E ON comment');
        $this->addSql('ALTER TABLE comment DROP user_id, DROP trick_id, CHANGE content content LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CB281BE2E');
        $this->addSql('DROP INDEX IDX_6A2CA10CB281BE2E ON media');
        $this->addSql('ALTER TABLE media DROP trick_id, CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE type type VARCHAR(10) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EA76ED395');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EC54C8C93');
        $this->addSql('DROP INDEX IDX_D8F0A91EA76ED395 ON trick');
        $this->addSql('DROP INDEX IDX_D8F0A91EC54C8C93 ON trick');
        $this->addSql('ALTER TABLE trick DROP user_id, DROP type_id, CHANGE title title VARCHAR(70) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE slug slug VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE content content LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE featured_picture featured_picture VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE type_trick CHANGE name name VARCHAR(60) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(60) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE lastname lastname VARCHAR(45) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE firstname firstname VARCHAR(45) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE token token VARCHAR(45) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE user_type user_type VARCHAR(10) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
