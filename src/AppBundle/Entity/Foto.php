<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Foto.
 *
 * @ORM\Table(name="foto")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\Repository\FotoRepository")
 */
class Foto
{
    use Timestampable;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Groups({
     *     "get_developer_by_id_admin", "get_admin_filter_developers",
     *     "get_admin_single_developer_by_id", "get_talent_file", "post_talent_file"
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255, nullable=true)
     * @Annotation\Groups({
     *     "get_developer_by_id_admin", "get_admin_filter_developers",
     *     "get_admin_single_developer_by_id", "get_talent_file", "post_talent_file"
     * })
     */
    private $filePath;
}
