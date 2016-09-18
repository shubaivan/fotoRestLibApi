<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints as AssertBridge;

/**
 * Tags.
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\TagsRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\HasLifecycleCallbacks
 * @AssertBridge\UniqueEntity(
 *     groups={"post_tag"},
 *     fields="tag",
 *     errorPath="not valid",
 *     message="This tag is already in use."
 * )
 */
class Tags
{
    const GROUP_GET_TAG = 'get_tag';
    const GROUP_POST_TAG = 'post_tag';
    
    use Timestampable;
    
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({"get_tag"})
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"post_tag"})
     * @ORM\Column(name="tag", type="string", length=255)
     * @Annotation\Groups({"get_tag", "post_tag"})
     */
    private $tag;

    /**
     * @var Photo[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Photo", mappedBy="tags")
     */
    protected $photo;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->photo = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Tags
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Add photo
     *
     * @param Photo $photo
     *
     * @return Tags
     */
    public function addPhoto(Photo $photo)
    {
        $this->photo[] = $photo;

        return $this;
    }

    /**
     * Remove photo
     *
     * @param Photo $photo
     */
    public function removePhoto(Photo $photo)
    {
        $this->photo->removeElement($photo);
    }

    /**
     * Get photo
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhoto()
    {
        return $this->photo;
    }
}
