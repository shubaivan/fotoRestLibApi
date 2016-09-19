<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * add table tags
 */
class Version20160918102344 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if (!$schema->hasTable('tags')) {
            $this->addSql('
                  CREATE TABLE `tags` (
                      `id` INT AUTO_INCREMENT NOT NULL, 
                      `tag` VARCHAR(255) NOT NULL, 
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
        if (!$schema->hasTable('tags')) {
            $this->addSql('DROP TABLE `tags`');
        }
    }
}
