<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
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
    const GROUP_PUT_PHOTO = 'put_photo';
    
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
     * @var Tags[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="Tags", inversedBy="photo")
     * @Annotation\Type("Relation<AppBundle\Entity\Tags>")
     * @Annotation\SerializedName("tag_ids")
     * @Annotation\Accessor(setter="setSerializedTag")
     * @Annotation\Groups({
     *     "post_photo", "get_photo", "put_photo"
     * })
     */
    protected $tags;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
    }

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

    /**
     * Add tag
     *
     * @param Tags $tag
     *
     * @return Photo
     */
    public function addTag(Tags $tag)
    {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tag
     *
     * @param Tags $tag
     */
    public function removeTag(Tags $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setSerializedTag (array $tags) {
        $this->tags = new ArrayCollection();
        if (!empty($tags)) {
            foreach ($tags as $tag) {
                $this->tags[] = $tag;
            }
        }

        if (empty($tags)) {
            $this->tags[] = new Tags();
        }
    }    
}
