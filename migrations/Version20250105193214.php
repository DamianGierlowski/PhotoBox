<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105193214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite ADD deleted TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE favorite RENAME INDEX idx_e46960f54e7af8f TO IDX_68C58ED94E7AF8F');
        $this->addSql('ALTER TABLE favorite RENAME INDEX idx_e46960f593cb796c TO IDX_68C58ED993CB796C');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite DROP deleted');
        $this->addSql('ALTER TABLE favorite RENAME INDEX idx_68c58ed993cb796c TO IDX_E46960F593CB796C');
        $this->addSql('ALTER TABLE favorite RENAME INDEX idx_68c58ed94e7af8f TO IDX_E46960F54E7AF8F');
    }
}
