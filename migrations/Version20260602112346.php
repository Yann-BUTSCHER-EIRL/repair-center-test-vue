<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260602112346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE labour_type_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE line_labour_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE line_part_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE quote_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE labour_type (id INT NOT NULL, label VARCHAR(255) NOT NULL, description TEXT NOT NULL, indicative_hourly_rate DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE line_labour (id INT NOT NULL, labour_type_id INT NOT NULL, quote_id INT NOT NULL, time_spent DOUBLE PRECISION NOT NULL, hourly_rate DOUBLE PRECISION NOT NULL, tax_percentage DOUBLE PRECISION NOT NULL, discount_percentage DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_200F48F4B9A0E379 ON line_labour (labour_type_id)');
        $this->addSql('CREATE INDEX IDX_200F48F4DB805178 ON line_labour (quote_id)');
        $this->addSql('CREATE TABLE line_part (id INT NOT NULL, part_id VARCHAR(255) NOT NULL, quote_id INT NOT NULL, quantity INT NOT NULL, tax_percentage DOUBLE PRECISION NOT NULL, discount_percentage DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C242831F4CE34BEC ON line_part (part_id)');
        $this->addSql('CREATE INDEX IDX_C242831FDB805178 ON line_part (quote_id)');
        $this->addSql('CREATE TABLE quote (id INT NOT NULL, repair_order_id VARCHAR(255) NOT NULL, reference VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6B71CBF4E4071493 ON quote (repair_order_id)');
        $this->addSql('COMMENT ON COLUMN quote.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE line_labour ADD CONSTRAINT FK_200F48F4B9A0E379 FOREIGN KEY (labour_type_id) REFERENCES labour_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE line_labour ADD CONSTRAINT FK_200F48F4DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE line_part ADD CONSTRAINT FK_C242831F4CE34BEC FOREIGN KEY (part_id) REFERENCES part (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE line_part ADD CONSTRAINT FK_C242831FDB805178 FOREIGN KEY (quote_id) REFERENCES quote (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quote ADD CONSTRAINT FK_6B71CBF4E4071493 FOREIGN KEY (repair_order_id) REFERENCES repair_order (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE customer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE part ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE part ALTER id DROP DEFAULT');
        $this->addSql('ALTER INDEX uniq_part_reference RENAME TO UNIQ_490F70C6AEA34913');
        $this->addSql('ALTER TABLE repair_order ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE repair_order ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE repair_order ALTER customer_id TYPE VARCHAR(255)');
        $this->addSql('ALTER INDEX uniq_repair_order_reference RENAME TO UNIQ_55F65734AEA34913');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE labour_type_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE line_labour_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE line_part_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE quote_id_seq CASCADE');
        $this->addSql('ALTER TABLE line_labour DROP CONSTRAINT FK_200F48F4B9A0E379');
        $this->addSql('ALTER TABLE line_labour DROP CONSTRAINT FK_200F48F4DB805178');
        $this->addSql('ALTER TABLE line_part DROP CONSTRAINT FK_C242831F4CE34BEC');
        $this->addSql('ALTER TABLE line_part DROP CONSTRAINT FK_C242831FDB805178');
        $this->addSql('ALTER TABLE quote DROP CONSTRAINT FK_6B71CBF4E4071493');
        $this->addSql('DROP TABLE labour_type');
        $this->addSql('DROP TABLE line_labour');
        $this->addSql('DROP TABLE line_part');
        $this->addSql('DROP TABLE quote');
        $this->addSql('ALTER TABLE part ALTER id TYPE INT');
        $this->addSql('CREATE SEQUENCE part_id_seq');
        $this->addSql('SELECT setval(\'part_id_seq\', (SELECT MAX(id) FROM part))');
        $this->addSql('ALTER TABLE part ALTER id SET DEFAULT nextval(\'part_id_seq\')');
        $this->addSql('ALTER INDEX uniq_490f70c6aea34913 RENAME TO uniq_part_reference');
        $this->addSql('ALTER TABLE customer ALTER id TYPE INT');
        $this->addSql('CREATE SEQUENCE customer_id_seq');
        $this->addSql('SELECT setval(\'customer_id_seq\', (SELECT MAX(id) FROM customer))');
        $this->addSql('ALTER TABLE customer ALTER id SET DEFAULT nextval(\'customer_id_seq\')');
        $this->addSql('ALTER TABLE repair_order ALTER id TYPE INT');
        $this->addSql('CREATE SEQUENCE repair_order_id_seq');
        $this->addSql('SELECT setval(\'repair_order_id_seq\', (SELECT MAX(id) FROM repair_order))');
        $this->addSql('ALTER TABLE repair_order ALTER id SET DEFAULT nextval(\'repair_order_id_seq\')');
        $this->addSql('ALTER TABLE repair_order ALTER customer_id TYPE INT');
        $this->addSql('ALTER INDEX uniq_55f65734aea34913 RENAME TO uniq_repair_order_reference');
    }
}
