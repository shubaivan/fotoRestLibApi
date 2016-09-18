<?php

namespace AppBundle\Application\Photo;

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
}
