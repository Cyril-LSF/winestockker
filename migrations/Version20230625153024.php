<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230625153024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE quantity (id INT AUTO_INCREMENT NOT NULL, cellar_id INT NOT NULL, botlle_id INT NOT NULL, quanty VARCHAR(7) NOT NULL, INDEX IDX_9FF31636D4A8C468 (cellar_id), INDEX IDX_9FF31636337CB5DD (botlle_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quantity ADD CONSTRAINT FK_9FF31636D4A8C468 FOREIGN KEY (cellar_id) REFERENCES cellar (id)');
        $this->addSql('ALTER TABLE quantity ADD CONSTRAINT FK_9FF31636337CB5DD FOREIGN KEY (botlle_id) REFERENCES bottle (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE quantity DROP FOREIGN KEY FK_9FF31636D4A8C468');
        $this->addSql('ALTER TABLE quantity DROP FOREIGN KEY FK_9FF31636337CB5DD');
        $this->addSql('DROP TABLE quantity');
    }
}
