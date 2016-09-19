<?php

namespace AppBundle\Entity\Repository\RepoInterface;

use AppBundle\Entity\Tags;

interface TagsRepositoryInterface
{
    /**
     * @param Tags $entity
     * @return void
     */
    public function removeEntityFlush(Tags $entity);

    /**
     * @param Tags $entity
     * @return void
     */
    public function removeEntity(Tags $entity);

    /**
     * @param Tags $entity
     * @return void
     */
    public function postEntity(Tags $entity);

    /**
     * @param Tags $entity
     * @return void
     */
    public function persistEntity(Tags $entity);

    /**
     * @return void
     */
    public function flushEntity();
}