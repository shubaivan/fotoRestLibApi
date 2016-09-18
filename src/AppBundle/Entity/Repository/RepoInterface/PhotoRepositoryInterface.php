<?php

namespace AppBundle\Entity\Repository\RepoInterface;

use AppBundle\Entity\Photo;

interface PhotoRepositoryInterface
{
    /**
     * @param Photo $entity
     * @return void
     */
    public function removeEntityFlush(Photo $entity);

    /**
     * @param Photo $entity
     * @return void
     */
    public function removeEntity(Photo $entity);

    /**
     * @param Photo $entity
     * @return void
     */
    public function postEntity(Photo $entity);

    /**
     * @param Photo $entity
     * @return void
     */
    public function persistEntity(Photo $entity);

    /**
     * @return void
     */
    public function flushEntity();
}