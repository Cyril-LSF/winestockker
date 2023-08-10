<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230809071618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD price_in_cents VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD invoice VARCHAR(255) DEFAULT NULL, ADD amount VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE transaction DROP invoice, DROP amount');
        $this->addSql('ALTER TABLE product DROP price_in_cents');
    }
}
