<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Repository\RepoInterface\PhotoRepositoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * PhotoRepository
 */
class PhotoRepository extends EntityRepository implements PhotoRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function removeEntityFlush(Photo $entity)
    {
        $this->removeEntity($entity);
        $this->flushEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function removeEntity(Photo $entity)
    {
        $this->_em->remove($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function postEntity(Photo $entity)
    {
        $this->persistEntity($entity);
        $this->flushEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function persistEntity(Photo $entity)
    {
        $this->_em->persist($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function flushEntity()
    {
        $this->_em->flush();
    }
}
