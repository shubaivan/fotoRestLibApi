<?php

namespace AppBundle\Entity\Repository\RepoInterface;

use AppBundle\Entity\Photo;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;

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

    /**
     * @param array $parameters
     * @return Photo|null
     */
    public function findEntityBy(array $parameters);

    /**
     * @param ParameterBag $parameterBag
     * @param ParamFetcher $paramFetcher
     * @param \DateTime|null $dateFrom
     * @param \DateTime|null $dateTo
     * @return Photo[]
     */
    public function getPhotoByParameters(
        ParameterBag $parameterBag,        
        ParamFetcher $paramFetcher,
        $dateFrom,
        $dateTo
    );    
}