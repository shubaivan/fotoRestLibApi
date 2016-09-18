<?php

namespace AppBundle\Application\Photo;

use AppBundle\Domain\Photo\PhotoInterface as DomainPhotoInterface;
use AppBundle\Exception\NotExistEntityException;
use AppBundle\Helper\FileUploader;
use AppBundle\Services\ObjectUpdater;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;
use \AppBundle\Entity\Photo as PhotoEntity; 

class Photo implements PhotoInterface
{
    /**
     * @var PhotoInterface
     */
    private $photo;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var ObjectUpdater
     */
    private $objectUpdater;

    /**
     * Photo constructor.
     * @param DomainPhotoInterface $photoInterface
     * @param FileUploader $fileUploader
     * @param ObjectUpdater $objectUpdater
     */
    public function __construct(
        DomainPhotoInterface $photoInterface,
        FileUploader $fileUploader,
        ObjectUpdater $objectUpdater
    ) {
        $this->photo = $photoInterface;
        $this->fileUploader = $fileUploader;
        $this->objectUpdater = $objectUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function postPhoto(
        ParameterBag $parameterBag,
        UploadedFile $file
    ) {
        $parameterBag->set('file_path', $this->fileUploader->uploadImage($file));
        return $this->postPhotoEntity($parameterBag);
    }

    /**
     * {@inheritdoc}
     */
    public function putPhoto(
        ParameterBag $parameterBag,
        $id
    ) {
        if (!$photo = $this->getDomainPhotoInterface()->findEntityBy(['id' => $id])) {
            throw new NotExistEntityException('No photo was found for this id '.$id);
        }
        $newPhoto = $this->getDomainPhotoInterface()->postPhoto($parameterBag, [PhotoEntity::GROUP_PUT_PHOTO]);
        $this->objectUpdater->updateObject($photo, $newPhoto);
        $this->getDomainPhotoInterface()->postRepositoryPhoto($photo);
        return $photo;
    }
    
    /**
     * {@inheritdoc}
     */
    public function postPhotoEntity(
        ParameterBag $parameterBag
    ) {
        $photo = $this->getDomainPhotoInterface()->postPhoto($parameterBag, [PhotoEntity::GROUP_POST_PHOTO]);
        $this->getDomainPhotoInterface()->validatePhoto($photo, [PhotoEntity::GROUP_POST_PHOTO]);
        $this->getDomainPhotoInterface()->postRepositoryPhoto($photo);
        return $photo;
    }

    /**
     * @return DomainPhotoInterface
     */
    private function getDomainPhotoInterface()
    {
        return $this->photo;
    }
}
