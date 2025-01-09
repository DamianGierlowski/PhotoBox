<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250102180431 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorites (id INT AUTO_INCREMENT NOT NULL, gallery_id INT NOT NULL, file_id INT NOT NULL, email VARCHAR(255) NOT NULL, guid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_E46960F54E7AF8F (gallery_id), INDEX IDX_E46960F593CB796C (file_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorites ADD CONSTRAINT FK_E46960F54E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE favorites ADD CONSTRAINT FK_E46960F593CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorites DROP FOREIGN KEY FK_E46960F54E7AF8F');
        $this->addSql('ALTER TABLE favorites DROP FOREIGN KEY FK_E46960F593CB796C');
        $this->addSql('DROP TABLE favorites');
    }
}
