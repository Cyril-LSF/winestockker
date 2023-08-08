<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230807071738 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD premium_to DATETIME DEFAULT NULL, DROP stripe_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD stripe_id VARCHAR(255) DEFAULT NULL, DROP premium_to');
    }
}
