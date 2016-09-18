<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * created relation tags photo
 */
class Version20160918164210 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $isNow = false;
        if (!$schema->hasTable('tags_photo')) {
            $this->addSql('
                CREATE TABLE `tags_photo` (
                    `tags_id` INT NOT NULL, 
                    `photo_id` INT NOT NULL,
                     INDEX `IDX_7FD372408D7B4FB4` (`tags_id`), 
                     INDEX `IDX_7FD372407E9E4C8C` (`photo_id`), 
                     PRIMARY KEY(`tags_id`, `photo_id`)
                 ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
            ');
            $isNow = true;
        }

        if ($isNow || ($schema->hasTable('tags_photo') && !$schema->getTable('tags_photo')->hasForeignKey('FK_7FD372408D7B4FB4'))) {
            $this->addSql('
                  ALTER TABLE `tags_photo` 
                  ADD CONSTRAINT `FK_7FD372408D7B4FB4` 
                  FOREIGN KEY (`tags_id`) 
                  REFERENCES `tags` (`id`) ON DELETE CASCADE
            ');
        }

        if ($isNow || ($schema->hasTable('tags_photo') && !$schema->getTable('tags_photo')->hasForeignKey('FK_7FD372407E9E4C8C'))) {
            $this->addSql('
                  ALTER TABLE `tags_photo` 
                  ADD CONSTRAINT `FK_7FD372407E9E4C8C` 
                  FOREIGN KEY (`photo_id`) 
                  REFERENCES `photo` (`id`) 
                  ON DELETE CASCADE
            ');
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE tags_photo');
    }
}
