<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250426073607 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE customer (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, full_name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_81398E09A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_81398E09A76ED395 ON customer (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE payement (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, rental_id INTEGER DEFAULT NULL, status_id INTEGER DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, method VARCHAR(255) NOT NULL, paid_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            , created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_B20A7885A7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B20A78856BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B20A7885A7CF2329 ON payement (rental_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B20A78856BF700BD ON payement (status_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rental (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, car_id INTEGER DEFAULT NULL, status_id INTEGER DEFAULT NULL, start_date DATE NOT NULL --(DC2Type:date_immutable)
            , end_date DATE NOT NULL --(DC2Type:date_immutable)
            , total_amount DOUBLE PRECISION NOT NULL, created_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , updated_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
            , CONSTRAINT FK_1619C27DC3C6F69F FOREIGN KEY (car_id) REFERENCES car (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_1619C27D6BF700BD FOREIGN KEY (status_id) REFERENCES status (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1619C27DC3C6F69F ON rental (car_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1619C27D6BF700BD ON rental (status_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rental_customer (rental_id INTEGER NOT NULL, customer_id INTEGER NOT NULL, PRIMARY KEY(rental_id, customer_id), CONSTRAINT FK_B15F973AA7CF2329 FOREIGN KEY (rental_id) REFERENCES rental (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_B15F973A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B15F973AA7CF2329 ON rental_customer (rental_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B15F973A9395C3F3 ON rental_customer (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE status (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, status_name VARCHAR(255) NOT NULL)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE customer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE payement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rental
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rental_customer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE status
        SQL);
    }
}
