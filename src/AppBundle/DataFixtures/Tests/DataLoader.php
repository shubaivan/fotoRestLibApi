<?php

namespace AppBundle\DataFixtures\Tests;

use Hautelook\AliceBundle\Alice\DataFixtureLoader;

class DataLoader extends DataFixtureLoader
{
    const FIXTURES_EXT = 'yml';

    /**
     * @var array
     */
    private $fixturesPaths = [];

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return $this->fixturesPaths;
    }

    /**
     * @param string|object $class
     * @param int|null $count
     * @param string|null $dir
     */
    public function addFixture($class, $count, $dir = null)
    {
        $className = class_exists($class) ? $this->parseClassName($class) : $class;

        $fixtureName = sprintf('%s/%s.%s', $dir, $className, self::FIXTURES_EXT);

        $fixturePath = $this->getSourceDir() . $fixtureName;

        if (file_exists($fixturePath)) {
            if ($count) {
                for ($i = 0; $i < $count; $i ++) {
                    $this->fixturesPaths[] = $fixturePath;
                }
            } else {
                $this->fixturesPaths[] = $fixturePath;
            }
        }
    }

    /**
     * Return current dir path.
     *
     * @return string
     */
    private function getSourceDir()
    {
        return __DIR__.'/Entity/';
    }

    /**
     * @param string|object $className
     *
     * @return string
     */
    private function parseClassName($className)
    {
        if (is_object($className)) {
            $className = get_class($className);
        }

        return preg_replace([
            "/^.*?\\\Entity\\\/",
            "/^.*?\/Entity\//",
            "/\.php/",
        ], '', $className);
    }
}
