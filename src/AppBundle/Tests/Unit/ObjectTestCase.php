<?php

namespace AppBundle\Tests\Unit;

use Doctrine\Common\Collections\ArrayCollection;

class ObjectTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param object|string $object - Class or object
     * @param string $attribute
     * @return mixed
     */
    public function getEntityAttr($object, $attribute)
    {
        $ref  = new \ReflectionClass($object);
        $prop = $ref->getProperty($attribute);
        $prop->setAccessible(true);
        $value = $prop->getValue($object);
        $prop->setAccessible(false);

        return $value;
    }

    /**
     * @param object|string $object - Class or object
     * @param string $attribute
     * @param mixed $value
     */
    public function setEntityAttr($object, $attribute, $value)
    {
        $ref  = new \ReflectionClass($object);
        $prop = $ref->getProperty($attribute);
        $prop->setAccessible(true);
        $prop->setValue($object, $value);
        $prop->setAccessible(false);
    }

    /**
     * @param object|string $object - Class or object
     * @param string $attribute
     * @param mixed $default
     */
    public function assertDefault($object, $attribute, $default)
    {
        switch (true) {
            case is_null($default):
                $this->assertNull($this->getEntityAttr($object, $attribute));
                break;
            default:
                $this->assertEquals($default, $this->getEntityAttr($object, $attribute));
        }
    }

    /**
     * @param object|string $object - Class or object
     * @param string $attribute
     * @param mixed $oldValue
     * @param mixed $newValue
     * @param null|string $method
     */
    public function assertSetter($object, $attribute, $oldValue, $newValue, $method = null)
    {
        $this->assertDefault($object, $attribute, $oldValue);

        if (!$method) {
            $method = 'set' . ucfirst($attribute);
        }

        $result = $object->{$method}(($newValue instanceof ArrayCollection) ? $newValue->toArray() : $newValue);

        $this->assertEquals($newValue, $this->getEntityAttr($object, $attribute));
        $this->assertInstanceOf(get_class($object), $result);
    }

    /**
     * @param object|string $object - Class or object
     * @param string $attribute
     * @param mixed $oldValue
     * @param mixed $newValue
     * @param null|string $method
     */
    public function assertGetter($object, $attribute, $oldValue, $newValue, $method = null)
    {
        if (!$method) {
            $method = 'get' . ucfirst($attribute);
        }

        $this->assertDefault($object, $attribute, $oldValue);
        $this->assertEquals($oldValue, $object->{$method}());

        $this->setEntityAttr($object, $attribute, $newValue);

        $this->assertEquals($newValue, $this->getEntityAttr($object, $attribute));
        $this->assertEquals($newValue, $object->{$method}());
    }

    /**
     * @param object|string $object - Class or object
     * @param string $attribute
     * @param mixed $defaultValue
     * @param mixed $newValue
     * @param null $methodGet
     * @param null $methodSet
     */
    public function assertBoth(
        $object,
        $attribute,
        $defaultValue,
        $newValue,
        $methodGet = null,
        $methodSet = null
    ) {
        $methodGet = $methodGet ? : 'get' . ucfirst($attribute);
        $methodSet = $methodSet ? : 'set' . ucfirst($attribute);

        $this->assertDefault($object, $attribute, $defaultValue);

        $this->assertEquals($defaultValue, $object->{$methodGet}());

        $result = $object->{$methodSet}(($newValue instanceof ArrayCollection) ? $newValue->toArray() : $newValue);
        $this->assertEquals($newValue, $this->getEntityAttr($object, $attribute));
        $this->assertInstanceOf(get_class($object), $result);

        $this->assertEquals($newValue, $this->getEntityAttr($object, $attribute));
        $this->assertEquals($newValue, $object->{$methodGet}());
    }
}
