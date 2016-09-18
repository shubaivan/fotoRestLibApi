<?php

namespace AppBundle\Services;

use AppBundle\Exception\NotExistEntityException;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class RelationsHandler
{
    const SET_NULL = 'setNull';
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * RelationsHandler constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager) { $this->manager = $manager; }


    /**
     * @param JsonSerializationVisitor $visitor
     * @param $relation
     * @param array $type
     * @param Context $context
     * @return array|mixed
     */
    public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context)
    {
        if ($relation instanceof \Traversable) {
            $relation = iterator_to_array($relation);
        }

        if (is_array($relation)) {
            return array_map([$this, 'getSingleEntityRelation'], $relation);
        }

        return $this->getSingleEntityRelation($relation);
    }

    /**
     * @param $relation
     *
     * @return array|mixed
     */
    protected function getSingleEntityRelation($relation)
    {
        $metadata = $this->manager->getClassMetadata(get_class($relation));

        $ids = $metadata->getIdentifierValues($relation);
        if (!$metadata->isIdentifierComposite) {
            $ids = array_shift($ids);
        }

        return $ids;
    }

    /**
     * @param $relation
     *
     * @return array|mixed
     */
    protected function getObjectEntityRelation($relation)
    {
        $metadata = $this->manager->getClassMetadata(get_class($relation));

        $ids = $metadata->getIdentifierValues($relation);

        return $ids;
    }

    /**
     * @param JsonDeserializationVisitor $visitor
     * @param $relation
     * @param array $type
     * @param Context $context
     * @return array|object|string
     */
    public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context)
    {
        $className = isset($type['params'][0]['name']) ? $type['params'][0]['name'] : null;

        if (!class_exists($className, false)) {
            throw new \InvalidArgumentException('Class name should be explicitly set for deserialization');
        }

        $metadata = $this->manager->getClassMetadata($className);

        if (!is_array($relation)) {
            if ($relation != 0) {
                $ifExist = $this->manager->getRepository($className)->find($relation);
                if (!$ifExist) {
                    throw new NotExistEntityException('not exist ' . $type['params'][0]['name'] . ' with this id ' . $relation);
                }
                $return = $this->manager->getReference($className, $relation);
            } else {
                $return = self::SET_NULL;
            }
            return $return;
        }

        $single = false;
        if ($metadata->isIdentifierComposite) {
            $single = true;
            foreach ($metadata->getIdentifierFieldNames() as $idName) {
                $single = $single && array_key_exists($idName, $relation);
            }
        }

        if ($single) {
            return $this->manager->getReference($className, $relation);
        }

        $objects = [];
        foreach ($relation as $idSet) {
            $ifExist = $this->manager->getRepository($className)->find($idSet);
            if (!$ifExist) {
                return $objects;
            }
            $objects[] = $this->manager->getReference($className, $idSet);
        }

        return $objects;
    }
}