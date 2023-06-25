<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230625161312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE quantity DROP FOREIGN KEY FK_9FF31636337CB5DD');
        $this->addSql('DROP INDEX IDX_9FF31636337CB5DD ON quantity');
        $this->addSql('ALTER TABLE quantity CHANGE botlle_id bottle_id INT NOT NULL');
        $this->addSql('ALTER TABLE quantity ADD CONSTRAINT FK_9FF31636DCF9352B FOREIGN KEY (bottle_id) REFERENCES bottle (id)');
        $this->addSql('CREATE INDEX IDX_9FF31636DCF9352B ON quantity (bottle_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE quantity DROP FOREIGN KEY FK_9FF31636DCF9352B');
        $this->addSql('DROP INDEX IDX_9FF31636DCF9352B ON quantity');
        $this->addSql('ALTER TABLE quantity CHANGE bottle_id botlle_id INT NOT NULL');
        $this->addSql('ALTER TABLE quantity ADD CONSTRAINT FK_9FF31636337CB5DD FOREIGN KEY (botlle_id) REFERENCES bottle (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9FF31636337CB5DD ON quantity (botlle_id)');
    }
}
