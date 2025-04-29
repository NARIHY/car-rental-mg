<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429075153 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__payement AS SELECT id, rental_id, status_id, amount, method, paid_at, created_at, updated_at FROM payement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, rental_id INTEGER DEFAULT NULL, status_id INTEGER DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, method VARCHAR(255) NOT NULL, paid_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_B20A7885A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B20A78856BF700BD FOREIGN KEY (status_id) REFERENCES status (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO payement (id, rental_id, status_id, amount, method, paid_at, created_at, updated_at) SELECT id, rental_id, status_id, amount, method, paid_at, created_at, updated_at FROM __temp__payement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__payement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B20A78856BF700BD ON payement (status_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B20A7885A7CF2329 ON payement (rental_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__payement AS SELECT id, rental_id, status_id, amount, method, paid_at, created_at, updated_at FROM payement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, rental_id INTEGER DEFAULT NULL, status_id INTEGER DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, method VARCHAR(255) NOT NULL, paid_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_B20A7885A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B20A78856BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO payement (id, rental_id, status_id, amount, method, paid_at, created_at, updated_at) SELECT id, rental_id, status_id, amount, method, paid_at, created_at, updated_at FROM __temp__payement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__payement
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B20A7885A7CF2329 ON payement (rental_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B20A78856BF700BD ON payement (status_id)
        SQL);
    }
}
