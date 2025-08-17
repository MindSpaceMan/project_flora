<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250817193721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product_image ADD storage VARCHAR(20) DEFAULT \'external\' NOT NULL');
        $this->addSql('ALTER TABLE product_image ADD local_path VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE product_image ALTER url DROP NOT NULL');
        $this->addSql('ALTER TABLE product_image ALTER url TYPE VARCHAR(1024)');
        $this->addSql('ALTER TABLE product_image ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE product_image ALTER updated_at DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE product_image DROP storage');
        $this->addSql('ALTER TABLE product_image DROP local_path');
        $this->addSql('ALTER TABLE product_image ALTER url SET NOT NULL');
        $this->addSql('ALTER TABLE product_image ALTER url TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE product_image ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE product_image ALTER updated_at SET NOT NULL');
    }
}
