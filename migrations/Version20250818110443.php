<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250818110443 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "order" ADD cart_token_hash VARCHAR(128) DEFAULT NULL');
        $this->addSql('ALTER TABLE "order" ALTER status SET DEFAULT \'cart\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F5299398D3D62728 ON "order" (cart_token_hash)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_F5299398D3D62728');
        $this->addSql('ALTER TABLE "order" DROP cart_token_hash');
        $this->addSql('ALTER TABLE "order" ALTER status SET DEFAULT \'new\'');
    }
}
