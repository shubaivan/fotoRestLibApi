<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\Repository\RepoInterface\TagsRepositoryInterface;
use AppBundle\Entity\Tags;
use Doctrine\ORM\EntityRepository;

/**
 * TagsRepository
 */
class TagsRepository extends EntityRepository implements TagsRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function removeEntityFlush(Tags $entity)
    {
        $this->removeEntity($entity);
        $this->flushEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function removeEntity(Tags $entity)
    {
        $this->_em->remove($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function postEntity(Tags $entity)
    {
        $this->persistEntity($entity);
        $this->flushEntity();
    }

    /**
     * {@inheritdoc}
     */
    public function persistEntity(Tags $entity)
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
