<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230625152216 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bottle DROP quantity');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bottle ADD quantity VARCHAR(7) NOT NULL');
    }
}
