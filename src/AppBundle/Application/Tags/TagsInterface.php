<?php

namespace AppBundle\Application\Tags;

use AppBundle\Entity\Tags as TagEntity;
use Symfony\Component\HttpFoundation\ParameterBag;

interface TagsInterface
{
    /**
     * @param ParameterBag $parameterBag
     * @return TagEntity
     */
    public function postTagEntity(
        ParameterBag $parameterBag
    );
}
