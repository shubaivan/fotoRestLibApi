<?php

namespace AppBundle\Services;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Serializer;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class ObjectUpdater
{
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function updateObject($objectOld, $objectNew)
    {
        if (get_class($objectOld) != get_class($objectNew)) {
            throw new \Exception('class not equals');
        }

        $accessor = new PropertyAccessor();

        $reflect = new \ReflectionClass($objectOld);
        $properties = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }
            
            $propertyName = $property->getName();

            $newValue = $accessor->getValue($objectNew, $propertyName);
            if ($newValue instanceof ArrayCollection && !$newValue->getValues()) {
                continue;
            } elseif ($newValue instanceof ArrayCollection && $valueCollection = $newValue->getValues()) {
                foreach ($valueCollection as $value) {
                    if (!$value->getId()) {
                        $newValue->removeElement($value);
                    }
                }
            }
            
            if (
                $newValue !== null &&
                !is_array($newValue) &&
                $newValue !== RelationsHandler::SET_NULL
            ) {
                $accessor->setValue($objectOld, $propertyName, $newValue);
            } elseif ($newValue === RelationsHandler::SET_NULL) {
                $accessor->setValue($objectOld, $propertyName, null);
            }
        }

        return $objectOld;
    }
}
