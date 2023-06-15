<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230612182116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, name VARCHAR(255) NOT NULL, street_number VARCHAR(10) NOT NULL, street_number_extension VARCHAR(20) DEFAULT NULL, street_type VARCHAR(255) NOT NULL, street_name VARCHAR(100) NOT NULL, complement VARCHAR(255) DEFAULT NULL, postalcode VARCHAR(10) NOT NULL, city VARCHAR(200) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_D4E6F81B03A8386 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81B03A8386 FOREIGN KEY (created_by) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81B03A8386');
        $this->addSql('DROP TABLE address');
    }
}
