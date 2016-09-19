<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @Annotation\ExclusionPolicy("all")
 */
trait Timestampable
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Annotation\Expose()
     * @Annotation\Groups({
     *      "for_notification_by_project_id", "for_project", "for_all_projects", "get_team", "for_profile_project",
     *      "for_project_bit", "get_all_leads_admin", "get_all_leads", "get_apifullcontact_by_id",
     *      "get_entity_comments", "get_developer_by_id_admin", "for_project_bit_admin"
     * })
     * @Annotation\Type("DateTime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Annotation\Expose()
     * @Annotation\Groups({
     *      "for_project", "for_all_projects", "for_profile_project", "for_project_bit", "get_all_leads_admin",
     *      "get_all_leads", "get_apifullcontact_by_id"
     * })
     * @Annotation\Type("DateTime")
     */
    protected $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     * @Annotation\Type("DateTime")
     * @Annotation\Expose()
     */
    protected $deletedAt;

    /**
     * Set createdAt.
     *
     * @param \DateTime $createdAt
     *
     * @return parent
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt.
     *
     * @return \DateTime $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt.
     *
     * @param \DateTime $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt.
     *
     * @return \DateTime $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set deletedAt.
     *
     * @param \DateTime $deletedAt
     *
     * @return $this
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt.
     *
     * @return \DateTime $deletedAt
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
