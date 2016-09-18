<?php

namespace AppBundle\Domain\Photo;

use \AppBundle\Entity\Photo as PhotoEntity;
use AppBundle\Exception\DeserializeException;
use AppBundleBundle\Exception\ValidatorException;
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
    public function validateOrder(
        PhotoEntity $photo,
        array $group = []
    );
}
