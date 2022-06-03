<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220512140350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brands (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cart_items (id INT AUTO_INCREMENT NOT NULL, cart_owner_id INT NOT NULL, expire_date DATETIME NOT NULL, created_at DATETIME NOT NULL, paid TINYINT(1) NOT NULL, shipped TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_BEF48445E0A32E92 (cart_owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_3AF34668727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, brands_id INT DEFAULT NULL, logo_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, description LONGTEXT NOT NULL, inventory INT DEFAULT NULL, abv DOUBLE PRECISION DEFAULT NULL, featured TINYINT(1) NOT NULL, offer TINYINT(1) NOT NULL, ibu INT DEFAULT NULL, content VARCHAR(50) DEFAULT NULL, ingredients VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', combo_deal TINYINT(1) NOT NULL, INDEX IDX_B3BA5A5AE9EEC0C7 (brands_id), INDEX IDX_B3BA5A5AF98F144A (logo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, phone_number VARCHAR(50) NOT NULL, street VARCHAR(255) NOT NULL, postbox VARCHAR(10) NOT NULL, place VARCHAR(255) NOT NULL, age INT NOT NULL, created_at DATETIME NOT NULL, name VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, confirmation_token VARCHAR(40) DEFAULT NULL, password_change_date INT DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF48445E0A32E92 FOREIGN KEY (cart_owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AE9EEC0C7 FOREIGN KEY (brands_id) REFERENCES brands (id)');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AF98F144A FOREIGN KEY (logo_id) REFERENCES media (id)');
        $this->addSql('ALTER TABLE cart_products ADD CONSTRAINT FK_2D2515311AD5CDBF FOREIGN KEY (cart_id) REFERENCES cart_items (id)');
        $this->addSql('ALTER TABLE cart_products ADD CONSTRAINT FK_2D2515314584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_categories ADD CONSTRAINT FK_E8ACBE766C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_categories ADD CONSTRAINT FK_E8ACBE76A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_media ADD CONSTRAINT FK_8A755FDC6C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE products_media ADD CONSTRAINT FK_8A755FDCEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AE9EEC0C7');
        $this->addSql('ALTER TABLE cart_products DROP FOREIGN KEY FK_2D2515311AD5CDBF');
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE products_categories DROP FOREIGN KEY FK_E8ACBE76A21214B7');
        $this->addSql('ALTER TABLE cart_products DROP FOREIGN KEY FK_2D2515314584665A');
        $this->addSql('ALTER TABLE products_categories DROP FOREIGN KEY FK_E8ACBE766C8A81A9');
        $this->addSql('ALTER TABLE products_media DROP FOREIGN KEY FK_8A755FDC6C8A81A9');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF48445E0A32E92');
        $this->addSql('DROP TABLE brands');
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE products_media DROP FOREIGN KEY FK_8A755FDCEA9FDD75');
    }
}
