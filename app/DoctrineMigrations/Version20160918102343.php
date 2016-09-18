<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Add table photo
 */
class Version20160918102343 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('photo')) {
            $this->addSql('
                CREATE TABLE `photo` (
                    `id` INT AUTO_INCREMENT NOT NULL, 
                    `file_path` VARCHAR(255) DEFAULT NULL, 
                    `created_at` DATETIME NOT NULL, 
                    `updated_at` DATETIME NOT NULL, 
                    `deleted_at` DATETIME DEFAULT NULL, PRIMARY KEY(`id`)
                ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if (!$schema->hasTable('photo')) {
            $this->addSql('DROP TABLE `photo`');   
        }
    }
}
