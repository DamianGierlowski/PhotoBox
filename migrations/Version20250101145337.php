<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250101145337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commission ADD description LONGTEXT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1C6501582B6FCFB2 ON commission (guid)');
        $this->addSql('ALTER TABLE commission RENAME INDEX idx_7de1fd5aa76ed395 TO IDX_1C650158A76ED395');
        $this->addSql('ALTER TABLE gallery RENAME INDEX idx_472b783a71752335 TO IDX_472B783A202D1EB2');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_1C6501582B6FCFB2 ON commission');
        $this->addSql('ALTER TABLE commission DROP description');
        $this->addSql('ALTER TABLE commission RENAME INDEX idx_1c650158a76ed395 TO IDX_7DE1FD5AA76ED395');
        $this->addSql('ALTER TABLE gallery RENAME INDEX idx_472b783a202d1eb2 TO IDX_472B783A71752335');
    }
}
