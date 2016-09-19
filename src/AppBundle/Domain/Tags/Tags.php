<?php

namespace AppBundle\Domain\Tags;

use AppBundle\Entity\Repository\RepoInterface\PhotoRepositoryInterface;
use AppBundle\Entity\Repository\RepoInterface\TagsRepositoryInterface;
use AppBundle\Exception\DeserializeException;
use AppBundle\Exception\ValidatorException;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \AppBundle\Entity\Tags as TagsEntity;

class Tags implements TagsInterface
{
    /**
     * @var TagsRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Tags constructor.
     * @param TagsRepositoryInterface $repositoryInterface
     * @param Serializer $serializer
     * @param ValidatorInterface $validatorInterface
     */
    public function __construct(
        TagsRepositoryInterface $repositoryInterface,
        Serializer $serializer,
        ValidatorInterface $validatorInterface
    ) {
        $this->tagRepository = $repositoryInterface;
        $this->serializer = $serializer;
        $this->validator = $validatorInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function postTag(
        ParameterBag $parameterBag,
        array $group = []
    ) {
        $data = $parameterBag->all();
        $data = $this->serializer->serialize($data, 'json');
        $deserializedGroup = null;
        if ($group) {
            $deserializedGroup = DeserializationContext::create()
                ->setGroups($group);
        }

        try {
            /** @var TagsEntity $tagNew */
            $tagNew = $this->serializer->deserialize(
                $data,
                TagsEntity::class,
                'json',
                $deserializedGroup
            );
        } catch (\Exception $e) {
            throw new DeserializeException($e->getMessage());
        }

        return $tagNew;
    }

    /**
     * {@inheritdoc}
     */
    public function postRepositoryTag(
        TagsEntity $tags
    ) {
        $this->getTagRepository()->postEntity($tags);
    }

    /**
     * {@inheritdoc}
     */
    public function validateTag(
        TagsEntity $tags,
        array $group = []
    ) {
        $errors = $this->validator->validate(
            $tags,
            $group ? $group : null
        );
        if (count($errors)) {
            $validatorException = new ValidatorException();
            $validatorException->addError([$errors]);
            throw $validatorException;
        }

        return $tags;
    }

    /**
     * @return TagsRepositoryInterface
     */
    private function getTagRepository()
    {
        return $this->tagRepository;
    }
}
