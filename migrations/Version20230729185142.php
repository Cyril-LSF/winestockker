<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230729185142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE data_crypt (id INT AUTO_INCREMENT NOT NULL, credit_card_id INT NOT NULL, iv VARCHAR(255) NOT NULL, tag VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B44EC1047048FD0F (credit_card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE data_crypt ADD CONSTRAINT FK_B44EC1047048FD0F FOREIGN KEY (credit_card_id) REFERENCES credit_card (id)');
        $this->addSql('ALTER TABLE credit_card DROP iv');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE data_crypt DROP FOREIGN KEY FK_B44EC1047048FD0F');
        $this->addSql('DROP TABLE data_crypt');
        $this->addSql('ALTER TABLE credit_card ADD iv VARCHAR(255) NOT NULL');
    }
}
