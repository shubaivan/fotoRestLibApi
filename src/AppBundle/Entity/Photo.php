<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Photo.
 *
 * @ORM\Table(name="photo")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\PhotoRepository")
 */
class Photo
{
    const GROUP_POST_PHOTO = 'post_photo';
    const GROUP_GET_PHOTO = 'get_photo';
    
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_photo"
     * })
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(groups={"post_photo"})
     * @ORM\Column(name="file_path", type="string", length=255, nullable=true)
     * @Annotation\Groups({
     *     "post_photo", "get_photo"
     * })
     */
    private $filePath;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set filePath
     *
     * @param string $filePath
     *
     * @return Photo
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get filePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
