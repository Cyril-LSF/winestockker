<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230729083356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE credit_card (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, number VARCHAR(16) NOT NULL, expiration VARCHAR(5) NOT NULL, security_code VARCHAR(3) NOT NULL, INDEX IDX_11D627EEF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credit_card ADD CONSTRAINT FK_11D627EEF675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE credit_card DROP FOREIGN KEY FK_11D627EEF675F31B');
        $this->addSql('DROP TABLE credit_card');
    }
}
