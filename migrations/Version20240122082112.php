<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240122082112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demo ADD deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:deleted_at)\'');
        $this->addSql('CREATE INDEX IDX_D642DFA04AF38FD1 ON demo (deleted_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_D642DFA04AF38FD1 ON demo');
        $this->addSql('ALTER TABLE demo DROP deleted_at');
    }
}
