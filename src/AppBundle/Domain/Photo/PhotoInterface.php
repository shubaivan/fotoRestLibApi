<?php

namespace AppBundle\Domain\Photo;

use \AppBundle\Entity\Photo as PhotoEntity;
use AppBundle\Entity\Photo;
use AppBundle\Exception\DeserializeException;
use AppBundle\Exception\ValidatorException;
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
     * @param array $parameters
     * @return Photo|null
     */
    public function findEntityBy(array $parameters);
}
