<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;

/**
 * Tags.
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TagsRepository")
 */
class Tags
{
    use Timestampable;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({"get_directories_entities"})
     * @Annotation\Expose()
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=255)
     * @Annotation\Groups({"get_directories_entities"})
     * @Annotation\Expose()
     */
    private $tag;
}
