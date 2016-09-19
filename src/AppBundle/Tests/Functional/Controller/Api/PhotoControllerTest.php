<?php

namespace AppBundle\Tests\Functional\Controller\Api;

use AppBundle\Entity;
use AppBundle\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PhotoControllerTest extends FunctionalTestCase
{
    /**
     * @var Entity\Repository\TagsRepository
     */
    private $tagRepository;

    /**
     * @var Entity\Repository\PhotoRepository
     */
    private $photoRepository;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();

        $entityManager = $this->getEntityManager();
        $this->tagRepository = $entityManager->getRepository(Entity\Tags::class);
        $this->photoRepository = $entityManager->getRepository(Entity\Photo::class);

        $this->user                 = null;
    }

    public function testGetPhotoAction()
    {
        $this->loadFixtures([
            [Entity\Photo::class, 2],
            [Entity\Tags::class, 4]
        ]);

        /** @var Entity\Photo[] $photos */
        $photos = $this->photoRepository->findAll();
        $this->assertCount(2, $photos);
        /** @var Entity\Photo $photo1 */
        $photo1 = $photos[0];
        /** @var Entity\Photo $photo2 */
        $photo2 = $photos[1];
        
        /** @var Entity\Tags[] $tags */
        $tags = $this->tagRepository->findAll();
        $this->assertCount(4, $tags);
        
        /** @var Entity\Tags $tag1 */
        $tag1 = $tags[0];
        /** @var Entity\Tags $tag2 */        
        $tag2 = $tags[1];
        /** @var Entity\Tags $tag3 */
        $tag3 = $tags[2];
        /** @var Entity\Tags $tag4 */
        $tag4 = $tags[3];
        
        $tagIds = [];
        foreach ($tags as $tag){
            $tagIds [] = $tag->getId();
        }
        
        $photo1->addTags([$tag1, $tag2, $tag3]);
        $photo2->addTags([$tag4]);
        
        $this->getEntityManager()->persist($photo1);
        $this->getEntityManager()->persist($photo2);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $expected = [
            $photo1,
            $photo2
        ];
        
        $url = $this->generateUrl('get_photo', [], UrlGeneratorInterface::RELATIVE_PATH);
        $this->request(
            'get',
            $url,
            [],
            [
                'tag_ids' => $tagIds,
            ]
        );

        $this->assertEquals(200, $this->getResponseStatus());
        $data = json_decode($this->getResponseContent(), true);
        
        $this->validateTagData($expected, $data);
        
        $this->checkRoute(['get'], $this->generateUrl('get_photo', [], UrlGeneratorInterface::RELATIVE_PATH), [], [
            'tag_ids' => $tagIds,
        ]);        
    }

    public function testOneGetPhotoAction()
    {
        $this->loadFixtures([
            [Entity\Photo::class, 2],
            [Entity\Tags::class, 4]
        ]);

        /** @var Entity\Photo[] $photos */
        $photos = $this->photoRepository->findAll();
        $this->assertCount(2, $photos);
        /** @var Entity\Photo $photo1 */
        $photo1 = $photos[0];
        /** @var Entity\Photo $photo2 */
        $photo2 = $photos[1];

        /** @var Entity\Tags[] $tags */
        $tags = $this->tagRepository->findAll();
        $this->assertCount(4, $tags);

        /** @var Entity\Tags $tag1 */
        $tag1 = $tags[0];
        /** @var Entity\Tags $tag2 */
        $tag2 = $tags[1];
        /** @var Entity\Tags $tag3 */
        $tag3 = $tags[2];
        /** @var Entity\Tags $tag4 */
        $tag4 = $tags[3];

        $tagIds = [];
        foreach ($tags as $tag){
            $tagIds [] = $tag->getId();
        }

        $photo1->addTags([$tag1, $tag2, $tag3]);

        $this->getEntityManager()->persist($photo1);
        $this->getEntityManager()->persist($photo2);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $expected = [
            $photo1,
        ];

        $url = $this->generateUrl('get_photo', [], UrlGeneratorInterface::RELATIVE_PATH);
        $this->request(
            'get',
            $url,
            [],
            [
                'tag_ids' => $tagIds,
            ]
        );

        $this->assertEquals(200, $this->getResponseStatus());
        $data = json_decode($this->getResponseContent(), true);

        $this->validateTagData($expected, $data);
    }

    public function testNullGetPhotoAction()
    {
        $this->loadFixtures([
            [Entity\Photo::class, 2],
            [Entity\Tags::class, 4]
        ]);

        /** @var Entity\Photo[] $photos */
        $photos = $this->photoRepository->findAll();
        $this->assertCount(2, $photos);
        /** @var Entity\Photo $photo1 */
        $photo1 = $photos[0];
        /** @var Entity\Photo $photo2 */
        $photo2 = $photos[1];

        /** @var Entity\Tags[] $tags */
        $tags = $this->tagRepository->findAll();
        $this->assertCount(4, $tags);

        /** @var Entity\Tags $tag1 */
        $tag1 = $tags[0];
        /** @var Entity\Tags $tag2 */
        $tag2 = $tags[1];
        /** @var Entity\Tags $tag3 */
        $tag3 = $tags[2];
        /** @var Entity\Tags $tag4 */
        $tag4 = $tags[3];

        $tagIds = [
            $tag4->getId()
        ];

        $photo1->addTags([$tag1, $tag2, $tag3]);

        $this->getEntityManager()->persist($photo1);
        $this->getEntityManager()->persist($photo2);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();

        $url = $this->generateUrl('get_photo', [], UrlGeneratorInterface::RELATIVE_PATH);
        $this->request(
            'get',
            $url,
            [],
            [
                'tag_ids' => $tagIds,
            ]
        );

        $this->assertEquals(200, $this->getResponseStatus());
        $data = json_decode($this->getResponseContent(), true);
        $this->assertCount(0, $data);
    }

    /**
     * @param array $expected
     * @param array $data
     */
    private function validateTagData(array $expected, array $data)
    {
        $mapped = [];
        foreach ($expected as $expectedItem) {
            $mapped[$expectedItem->getId()] = $expectedItem;
        }

        foreach ($data as $dataItem) {
            $this->assertArrayHasKey('id', $dataItem);
            $id = $dataItem['id'];
            $this->assertArrayHasKey($id, $mapped);

            /** @var Entity\Photo $expectedItem */
            $expectedItem = $mapped[$id];
            $this->checkField('id', $dataItem, $expectedItem->getId());
        }
    }    
}
