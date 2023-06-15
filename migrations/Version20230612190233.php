<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230612190233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81B03A8386');
        $this->addSql('DROP INDEX IDX_D4E6F81B03A8386 ON address');
        $this->addSql('ALTER TABLE address CHANGE created_by author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D4E6F81F675F31B ON address (author_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE address DROP FOREIGN KEY FK_D4E6F81F675F31B');
        $this->addSql('DROP INDEX IDX_D4E6F81F675F31B ON address');
        $this->addSql('ALTER TABLE address CHANGE author_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE address ADD CONSTRAINT FK_D4E6F81B03A8386 FOREIGN KEY (created_by) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_D4E6F81B03A8386 ON address (created_by)');
    }
}
