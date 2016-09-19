<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * created relation photo tags
 */
class Version20160918172032 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $isNow = false;
        if (!$schema->hasTable('photo_tags')) {
            $this->addSql('
                CREATE TABLE `photo_tags` (
                    `photo_id` INT NOT NULL, 
                    `tags_id` INT NOT NULL, 
                    INDEX `IDX_EE8D26D27E9E4C8C` (`photo_id`), 
                    INDEX `IDX_EE8D26D28D7B4FB4` (`tags_id`), 
                    PRIMARY KEY(`photo_id`, `tags_id`)
                ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
            ');
        $isNow = true;
        }
        
        if ($isNow || ($schema->hasTable('photo_tags') && !$schema->getTable('photo_tags')->hasForeignKey('FK_EE8D26D27E9E4C8C'))) {
            $this->addSql('
                ALTER TABLE `photo_tags` 
                ADD CONSTRAINT `FK_EE8D26D27E9E4C8C` 
                FOREIGN KEY (`photo_id`) 
                REFERENCES `photo` (`id`) 
                ON DELETE CASCADE
            ');
        }
        
        if ($isNow || ($schema->hasTable('photo_tags') && !$schema->getTable('photo_tags')->hasForeignKey('FK_EE8D26D28D7B4FB4'))) {
            $this->addSql('
                ALTER TABLE `photo_tags` 
                ADD CONSTRAINT `FK_EE8D26D28D7B4FB4` 
                FOREIGN KEY (`tags_id`) 
                REFERENCES `tags` (`id`) ON DELETE CASCADE
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        if ($schema->hasTable('photo_tags')) {
            $this->addSql('DROP TABLE `photo_tags`');   
        }
    }
}
