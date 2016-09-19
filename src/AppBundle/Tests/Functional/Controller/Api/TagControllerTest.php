<?php

namespace AppBundle\Tests\Functional\Controller\Api;

use AppBundle\Entity;
use AppBundle\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TagControllerTest extends FunctionalTestCase
{
    /**
     * @var Entity\Repository\TagsRepository
     */
    private $tagRepository;

    /**
     * Set up
     */
    public function setUp()
    {
        parent::setUp();

        $entityManager = $this->getEntityManager();
        $this->tagRepository = $entityManager->getRepository(Entity\Tags::class);

        $this->user                 = null;
    }
    
    public function testPostTagAction()
    {
        $nameTag = 'new_test_tag';
        $expected = new Entity\Tags();
        $expected
            ->setTag('fixtures');
        
        $this->request(
            'post',
            $this->generateUrl('post_tag', [], UrlGeneratorInterface::RELATIVE_PATH),
            [],
            [
                'tag' => $nameTag,
            ]
        );

        $this->assertEquals(200, $this->getResponseStatus());
        $data = json_decode($this->getResponseContent(), true);

        $ref = new \ReflectionClass($expected);
        $prop = $ref->getProperty('id');
        $prop->setAccessible(true);
        $prop->setValue($expected, $data['id']);
        $prop->setAccessible(false);
        
        $this->validateTagData([$expected], [$data]);
        
        $this->checkRoute(['post'], $this->generateUrl('post_tag', [], UrlGeneratorInterface::RELATIVE_PATH), [], [
            'tag' => $nameTag,
        ]);        
    }
    
    public function testNegativePostTagAction()
    {
        $nameTag = 'new_test_tag';
        $expected = new Entity\Tags();
        $expected
            ->setTag($nameTag);
        $this->getEntityManager()->persist($expected);
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
        $this->request(
            'post',
            $this->generateUrl('post_tag', [], UrlGeneratorInterface::RELATIVE_PATH),
            [],
            [
                'tag' => $nameTag,
            ]
        );

        $this->assertEquals(400, $this->getResponseStatus());
    }
    
    public function testNegativePostEmptyTagAction()
    {
        $this->request(
            'post',
            $this->generateUrl('post_tag', [], UrlGeneratorInterface::RELATIVE_PATH),
            [],
            []
        );

        $this->assertEquals(400, $this->getResponseStatus());
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

            /** @var Entity\Tags $expectedItem */
            $expectedItem = $mapped[$id];
            $this->checkField('id', $dataItem, $expectedItem->getId());
            $this->assertNotEquals($dataItem['tag'], $expectedItem->getTag());
        }
    }    
}
