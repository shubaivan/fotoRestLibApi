<?php

namespace AppBundle\Application\Photo;

use AppBundle\Entity\Photo as PhotoEntity;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;

interface PhotoInterface
{
    /**
     * @param ParameterBag $parameterBag
     * @return mixed
     */
    public function postPhoto(
        ParameterBag $parameterBag,
        UploadedFile $file
    );

    /**
     * @param ParameterBag $parameterBag
     * @return mixed
     */
    public function postPhotoEntity(
        ParameterBag $parameterBag
    );

    /**
     * @param integer $id
     * @return void
     */
    public function removeEntity($id);
    
    /**
     * @param ParameterBag $parameterBag
     * @param integer $id
     * @return PhotoEntity
     */
    public function putPhoto(
        ParameterBag $parameterBag,
        $id
    );

    /**
     * @param ParameterBag $parameterBag
     * @param ParamFetcher $paramFetcher
     * @param string $dateFrom
     * @param string $dateTo
     * @return PhotoEntity[]
     */
    public function getPhotoByParameters(
        ParameterBag $parameterBag,
        ParamFetcher $paramFetcher,
        $dateFrom,
        $dateTo
    );    
}
