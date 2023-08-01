<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230731045709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_hash ADD payment_processor_id INT NOT NULL');
        $this->addSql('ALTER TABLE payment_hash ADD CONSTRAINT FK_F03BF5CC514A7680 FOREIGN KEY (payment_processor_id) REFERENCES payment_processor (id)');
        $this->addSql('CREATE INDEX IDX_F03BF5CC514A7680 ON payment_hash (payment_processor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_hash DROP FOREIGN KEY FK_F03BF5CC514A7680');
        $this->addSql('DROP INDEX IDX_F03BF5CC514A7680 ON payment_hash');
        $this->addSql('ALTER TABLE payment_hash DROP payment_processor_id');
    }
}
