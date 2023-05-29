<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230529123932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD birthday DATE NOT NULL, ADD firstname VARCHAR(50) NOT NULL, ADD lastname VARCHAR(50) NOT NULL, ADD screenname VARCHAR(52) DEFAULT NULL, ADD registered_at DATETIME NOT NULL, ADD premium TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP birthday, DROP firstname, DROP lastname, DROP screenname, DROP registered_at, DROP premium');
    }
}
