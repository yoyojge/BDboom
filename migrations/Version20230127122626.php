<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127122626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album_collection (id INT AUTO_INCREMENT NOT NULL, collection_id INT NOT NULL, INDEX IDX_F44D7617514956FD (collection_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wishlist_album (wishlist_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_6D3D06BBFB8E54CD (wishlist_id), INDEX IDX_6D3D06BB1137ABCF (album_id), PRIMARY KEY(wishlist_id, album_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album_collection ADD CONSTRAINT FK_F44D7617514956FD FOREIGN KEY (collection_id) REFERENCES collectionn (id)');
        $this->addSql('ALTER TABLE wishlist_album ADD CONSTRAINT FK_6D3D06BBFB8E54CD FOREIGN KEY (wishlist_id) REFERENCES wishlist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wishlist_album ADD CONSTRAINT FK_6D3D06BB1137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album_collection DROP FOREIGN KEY FK_F44D7617514956FD');
        $this->addSql('ALTER TABLE wishlist_album DROP FOREIGN KEY FK_6D3D06BBFB8E54CD');
        $this->addSql('ALTER TABLE wishlist_album DROP FOREIGN KEY FK_6D3D06BB1137ABCF');
        $this->addSql('DROP TABLE album_collection');
        $this->addSql('DROP TABLE wishlist_album');
    }
}
