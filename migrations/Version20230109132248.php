<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230109132248 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album_wishlist (id INT AUTO_INCREMENT NOT NULL, album_id INT NOT NULL, wishlist_id INT NOT NULL, INDEX IDX_6F0D8C241137ABCF (album_id), INDEX IDX_6F0D8C24FB8E54CD (wishlist_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE collectionn (id INT AUTO_INCREMENT NOT NULL, collector_id INT NOT NULL, collection_name VARCHAR(255) NOT NULL, INDEX IDX_B023BF37670BAFFE (collector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist (id INT AUTO_INCREMENT NOT NULL, collector_id INT NOT NULL, wishlist_name VARCHAR(255) NOT NULL, INDEX IDX_9CE12A31670BAFFE (collector_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album_wishlist ADD CONSTRAINT FK_6F0D8C241137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE album_wishlist ADD CONSTRAINT FK_6F0D8C24FB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id)');
        $this->addSql('ALTER TABLE collectionn ADD CONSTRAINT FK_B023BF37670BAFFE FOREIGN KEY (collector_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE wishlist ADD CONSTRAINT FK_9CE12A31670BAFFE FOREIGN KEY (collector_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album_wishlist DROP FOREIGN KEY FK_6F0D8C241137ABCF');
        $this->addSql('ALTER TABLE album_wishlist DROP FOREIGN KEY FK_6F0D8C24FB8E54CD');
        $this->addSql('ALTER TABLE collectionn DROP FOREIGN KEY FK_B023BF37670BAFFE');
        $this->addSql('ALTER TABLE wishlist DROP FOREIGN KEY FK_9CE12A31670BAFFE');
        $this->addSql('DROP TABLE album_wishlist');
        $this->addSql('DROP TABLE collectionn');
        $this->addSql('DROP TABLE wishlist');
    }
}
