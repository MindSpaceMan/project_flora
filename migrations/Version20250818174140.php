<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250818174140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE review DROP CONSTRAINT fk_794381c64584665a');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT fk_794381c69395c3f3');
        $this->addSql('DROP TABLE review');
        $this->addSql('ALTER TABLE address DROP country');
        $this->addSql('ALTER TABLE category ADD meta_title VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD meta_description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD comment TEXT NOT NULL');
        $this->addSql('ALTER TABLE customer ALTER email DROP NOT NULL');
        $this->addSql('ALTER TABLE "order" DROP total_amount');
        $this->addSql('ALTER TABLE "order" DROP currency');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE review (id UUID NOT NULL, product_id UUID NOT NULL, customer_id UUID DEFAULT NULL, rating SMALLINT NOT NULL, author_name VARCHAR(255) DEFAULT NULL, body TEXT NOT NULL, status VARCHAR(20) DEFAULT \'approved\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_794381c64584665a ON review (product_id)');
        $this->addSql('CREATE INDEX idx_794381c69395c3f3 ON review (customer_id)');
        $this->addSql('COMMENT ON COLUMN review.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN review.product_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN review.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN review.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT fk_794381c64584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT fk_794381c69395c3f3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD total_amount NUMERIC(12, 2) NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD currency VARCHAR(3) DEFAULT \'RUB\' NOT NULL');
        $this->addSql('ALTER TABLE customer DROP comment');
        $this->addSql('ALTER TABLE customer ALTER email SET NOT NULL');
        $this->addSql('ALTER TABLE address ADD country VARCHAR(100) NOT NULL');
        $this->addSql('ALTER TABLE category DROP meta_title');
        $this->addSql('ALTER TABLE category DROP meta_description');
    }
}
