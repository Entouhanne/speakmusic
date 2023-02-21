<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221226082705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE musique ADD id_auteur_id INT NOT NULL, DROP id_album, DROP id_auteur');
        $this->addSql('ALTER TABLE musique ADD CONSTRAINT FK_EE1D56BCE08ED3C1 FOREIGN KEY (id_auteur_id) REFERENCES auteur (id)');
        $this->addSql('CREATE INDEX IDX_EE1D56BCE08ED3C1 ON musique (id_auteur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE musique DROP FOREIGN KEY FK_EE1D56BCE08ED3C1');
        $this->addSql('DROP INDEX IDX_EE1D56BCE08ED3C1 ON musique');
        $this->addSql('ALTER TABLE musique ADD id_auteur INT NOT NULL, CHANGE id_auteur_id id_album INT NOT NULL');
    }
}
