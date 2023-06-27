<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;


final class Version20230627123908 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_64C19C1F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_bottle (category_id INT NOT NULL, bottle_id INT NOT NULL, INDEX IDX_A341632E12469DE2 (category_id), INDEX IDX_A341632EDCF9352B (bottle_id), PRIMARY KEY(category_id, bottle_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE category_bottle ADD CONSTRAINT FK_A341632E12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_bottle ADD CONSTRAINT FK_A341632EDCF9352B FOREIGN KEY (bottle_id) REFERENCES bottle (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1F675F31B');
        $this->addSql('ALTER TABLE category_bottle DROP FOREIGN KEY FK_A341632E12469DE2');
        $this->addSql('ALTER TABLE category_bottle DROP FOREIGN KEY FK_A341632EDCF9352B');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_bottle');
    }
}
