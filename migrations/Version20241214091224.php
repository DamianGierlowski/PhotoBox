<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241214091224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gallery_file (gallery_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_1F801E9C4E7AF8F (gallery_id), INDEX IDX_1F801E9C93CB796C (file_id), PRIMARY KEY(gallery_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE gallery_file ADD CONSTRAINT FK_1F801E9C4E7AF8F FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE gallery_file ADD CONSTRAINT FK_1F801E9C93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE gallery_file DROP FOREIGN KEY FK_1F801E9C4E7AF8F');
        $this->addSql('ALTER TABLE gallery_file DROP FOREIGN KEY FK_1F801E9C93CB796C');
        $this->addSql('DROP TABLE gallery_file');
    }
}
