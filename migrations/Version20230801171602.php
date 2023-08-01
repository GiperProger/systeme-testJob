<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230801171602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coupon (id INT AUTO_INCREMENT NOT NULL, type_id INT NOT NULL, discount_value INT NOT NULL, code VARCHAR(255) NOT NULL, INDEX IDX_64BF3F02C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coupon_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_hash (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, payment_processor_id INT NOT NULL, hash VARCHAR(255) NOT NULL, total_price DOUBLE PRECISION NOT NULL, INDEX IDX_F03BF5CC4584665A (product_id), INDEX IDX_F03BF5CC514A7680 (payment_processor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_processor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6AE969785E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, price SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tax (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, format VARCHAR(255) NOT NULL, percent SMALLINT NOT NULL, UNIQUE INDEX UNIQ_8E81BA76F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F02C54C8C93 FOREIGN KEY (type_id) REFERENCES coupon_type (id)');
        $this->addSql('ALTER TABLE payment_hash ADD CONSTRAINT FK_F03BF5CC4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE payment_hash ADD CONSTRAINT FK_F03BF5CC514A7680 FOREIGN KEY (payment_processor_id) REFERENCES payment_processor (id)');
        $this->addSql('ALTER TABLE tax ADD CONSTRAINT FK_8E81BA76F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F02C54C8C93');
        $this->addSql('ALTER TABLE payment_hash DROP FOREIGN KEY FK_F03BF5CC4584665A');
        $this->addSql('ALTER TABLE payment_hash DROP FOREIGN KEY FK_F03BF5CC514A7680');
        $this->addSql('ALTER TABLE tax DROP FOREIGN KEY FK_8E81BA76F92F3E70');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE coupon');
        $this->addSql('DROP TABLE coupon_type');
        $this->addSql('DROP TABLE payment_hash');
        $this->addSql('DROP TABLE payment_processor');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE tax');
    }
}
