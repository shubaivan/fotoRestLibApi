<?php

namespace AppBundle\Application\Tags;

use AppBundle\Domain\Tags\TagsInterface as DomainTagsInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use \AppBundle\Entity\Tags as TagEntity;

class Tags implements TagsInterface
{
    /**
     * @var DomainTagsInterface
     */
    private $tags;

    /**
     * Tags constructor.
     * @param DomainTagsInterface $tagsInterface
     */
    public function __construct(
        DomainTagsInterface $tagsInterface
    ) {
        $this->tags = $tagsInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function postTagEntity(
        ParameterBag $parameterBag
    ) {
        $tag = $this->getDomainTagsInterface()->postTag($parameterBag, [TagEntity::GROUP_POST_TAG]);
        $this->getDomainTagsInterface()->validateTag($tag, [TagEntity::GROUP_POST_TAG]);
        $this->getDomainTagsInterface()->postRepositoryTag($tag);
        return $tag;
    }

    /**
     * @return DomainTagsInterface
     */
    private function getDomainTagsInterface()
    {
        return $this->tags;
    }
}
