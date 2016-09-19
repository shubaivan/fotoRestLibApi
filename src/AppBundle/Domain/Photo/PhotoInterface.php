<?php

namespace AppBundle\Domain\Photo;

use \AppBundle\Entity\Photo as PhotoEntity;
use AppBundle\Exception\DeserializeException;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\ParameterBag;

interface PhotoInterface
{
    /**
     * @param ParameterBag $parameterBag
     * @param array $group
     * @return PhotoEntity|DeserializeException
     */
    public function postPhoto(
        ParameterBag $parameterBag,
        array $group = []
    );

    /**
     * @param PhotoEntity $photo
     * @return void
     */
    public function postRepositoryPhoto(
        PhotoEntity $photo
    );

    /**
     * @param PhotoEntity $photo
     * @param array $group
     * @return PhotoEntity|ValidatorException
     */
    public function validatePhoto(
        PhotoEntity $photo,
        array $group = []
    );

    /**
     * @param PhotoEntity $photo
     * @return void
     */
    public function removeEntity(PhotoEntity $photo);
    
    /**
     * @param array $parameters
     * @return PhotoEntity|null
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
