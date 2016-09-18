<?php

namespace AppBundle\Application\Tags;

use AppBundle\Entity\Tags;
use Symfony\Component\HttpFoundation\ParameterBag;

interface TagsInterface
{
    /**
     * @param ParameterBag $parameterBag
     * @return Tags
     */
    public function postTagEntity(
        ParameterBag $parameterBag
    );
}
