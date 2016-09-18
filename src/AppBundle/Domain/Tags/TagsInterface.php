<?php

namespace AppBundle\Domain\Tags;

use \AppBundle\Entity\Tags as TagsEntity;
use Symfony\Component\HttpFoundation\ParameterBag;

interface TagsInterface
{
    /**
     * @param ParameterBag $parameterBag
     * @param array $group
     * @return TagsEntity
     */
    public function postTag(
        ParameterBag $parameterBag,
        array $group = []
    );

    /**
     * @param TagsEntity $tags
     * @return void
     */
    public function postRepositoryTag(
        TagsEntity $tags
    );

    /**
     * @param TagsEntity $tags
     * @param array $group
     * @return TagsEntity
     */
    public function validateTag(
        TagsEntity $tags,
        array $group = []
    );
}
