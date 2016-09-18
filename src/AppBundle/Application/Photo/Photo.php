<?php

namespace AppBundle\Application\Photo;

use AppBundle\Domain\Photo\PhotoInterface as DomainPhotoInterface;
use AppBundle\Helper\FileUploader;
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
     * Photo constructor.
     * @param DomainPhotoInterface $photoInterface
     * @param FileUploader $fileUploader
     */
    public function __construct(
        DomainPhotoInterface $photoInterface,
        FileUploader $fileUploader
    ) {
        $this->photo = $photoInterface;
        $this->fileUploader = $fileUploader;
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
