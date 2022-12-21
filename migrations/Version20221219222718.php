<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219222718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `voucher` (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(32) NOT NULL, name VARCHAR(32) NOT NULL, description VARCHAR(255) DEFAULT NULL, discount_amount SMALLINT NOT NULL, expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', used SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `voucher` ADD INDEX `Index 2` (`expires_at`, `used`)');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, purchased_date DATETIME NOT NULL, amount INT NOT NULL, voucher_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT `FK_order_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `voucher` (`id`)');
        $this->addSql('ALTER TABLE `order` ADD INDEX `Index 3` (`purchased_date`)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `voucher` DROP INDEX `Index 2`');
        $this->addSql('DROP TABLE `voucher`');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY `FK_order_voucher`');
        $this->addSql('ALTER TABLE `order` DROP INDEX `Index 3`');
        $this->addSql('DROP TABLE `order`');
    }
}
