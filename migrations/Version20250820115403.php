<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250820115403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F81D7D3AC2A ON address (line1)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F814EDAFD90 ON address (line2)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D4E6F81421D9546 ON address (zip)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_D4E6F81D7D3AC2A');
        $this->addSql('DROP INDEX UNIQ_D4E6F814EDAFD90');
        $this->addSql('DROP INDEX UNIQ_D4E6F81421D9546');
    }
}
