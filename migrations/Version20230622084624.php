<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230622084624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bottle_cellar (bottle_id INT NOT NULL, cellar_id INT NOT NULL, INDEX IDX_50E72E8CDCF9352B (bottle_id), INDEX IDX_50E72E8CD4A8C468 (cellar_id), PRIMARY KEY(bottle_id, cellar_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bottle_cellar ADD CONSTRAINT FK_50E72E8CDCF9352B FOREIGN KEY (bottle_id) REFERENCES bottle (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bottle_cellar ADD CONSTRAINT FK_50E72E8CD4A8C468 FOREIGN KEY (cellar_id) REFERENCES cellar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE bottle ADD author_id INT NOT NULL');
        $this->addSql('ALTER TABLE bottle ADD CONSTRAINT FK_ACA9A955F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_ACA9A955F675F31B ON bottle (author_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE bottle_cellar DROP FOREIGN KEY FK_50E72E8CDCF9352B');
        $this->addSql('ALTER TABLE bottle_cellar DROP FOREIGN KEY FK_50E72E8CD4A8C468');
        $this->addSql('DROP TABLE bottle_cellar');
        $this->addSql('ALTER TABLE bottle DROP FOREIGN KEY FK_ACA9A955F675F31B');
        $this->addSql('DROP INDEX IDX_ACA9A955F675F31B ON bottle');
        $this->addSql('ALTER TABLE bottle DROP author_id');
    }
}
