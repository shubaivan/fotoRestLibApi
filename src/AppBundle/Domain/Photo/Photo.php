<?php

namespace AppBundle\Domain\Photo;

use AppBundle\Entity\Repository\RepoInterface\PhotoRepositoryInterface;
use AppBundle\Exception\DeserializeException;
use AppBundle\Exception\ValidatorException;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \AppBundle\Entity\Photo as PhotoEntity;

class Photo implements PhotoInterface
{
    /**
     * @var PhotoRepositoryInterface
     */
    private $photoRepository;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Photo constructor.
     * @param PhotoRepositoryInterface $photoRepositoryInterface
     * @param Serializer $serializer
     * @param ValidatorInterface $validatorInterface
     */
    public function __construct(
        PhotoRepositoryInterface $photoRepositoryInterface,
        Serializer $serializer,
        ValidatorInterface $validatorInterface
    ) {
        $this->photoRepository = $photoRepositoryInterface;
        $this->serializer = $serializer;
        $this->validator = $validatorInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function postPhoto(
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
            /** @var PhotoEntity $photoNew */
            $photoNew = $this->serializer->deserialize(
                $data,
                PhotoEntity::class,
                'json',
                $deserializedGroup
            );
        } catch (\Exception $e) {
            throw new DeserializeException($e->getMessage());
        }

        return $photoNew;
    }

    /**
     * {@inheritdoc}
     */
    public function postRepositoryPhoto(
        PhotoEntity $photo
    ) {
        $this->getPhotoRepository()->postEntity($photo);
    }

    /**
     * {@inheritdoc}
     */
    public function validatePhoto(
        PhotoEntity $photo,
        array $group = []
    ) {
        $errors = $this->validator->validate(
            $photo,
            $group ? $group : null
        );
        if (count($errors)) {
            $validatorException = new ValidatorException();
            $validatorException->addError([$errors]);
            throw $validatorException;
        }

        return $photo;
    }

    /**
     * {@inheritdoc}
     */
    public function findEntityBy(array $parameters)
    {
        return $this->getPhotoRepository()->findEntityBy($parameters);
    }

    /**
     * @return PhotoRepositoryInterface
     */
    private function getPhotoRepository()
    {
        return $this->photoRepository;
    }
}
