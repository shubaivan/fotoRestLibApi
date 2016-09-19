<?php

namespace AppBundle\Tests\Integration;

use AppBundle\DataFixtures\Tests\DataLoader;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OrmTestCase.
 *
 * This is the base class to load doctrine fixtures using the symfony configuration
 */
class OrmTestCase extends WebTestCase
{
    const DATE_FORMAT = DATE_ISO8601;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var string
     */
    protected $environment = 'test';

    /**
     * @var bool
     */
    protected $debug = true;

    /**
     * @var string
     */
    protected $entityManagerServiceId = 'doctrine.orm.entity_manager';

    /**
     * @param bool $createSchema
     *
     * @throws ToolsException
     */
    public function setUp($createSchema = true)
    {
        parent::setUp();

        $createSchema && $this->createSchema();
    }

    /**
     * Constructor.
     *
     * @param string|null $name     Test name
     * @param array       $data     Test data
     * @param string      $dataName Data name
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (null !== static::$kernel) {
            static::$kernel->shutdown();
        }

        static::$kernel = static::createKernel([
            'environment' => $this->environment,
            'debug' => $this->debug,
        ]);
        static::$kernel->boot();

        $this->container = static::$kernel->getContainer();
        $this->em = $this->getEntityManager();
    }

    /**
     * Creates database schema.
     *
     * @throws ToolsException
     */
    protected function createSchema()
    {
        $registry = new Registry(
            $this->getContainer(),
            [],
            ['default' => 'doctrine.orm.default_entity_manager'],
            'default',
            'default'
        );
        $registry->resetManager();

        $em = $this->getEntityManager();
        $schemaTool = new SchemaTool($em);
        $schemaTool->dropDatabase();

        $conn = $em->getConnection();
        foreach ($this->getSchemaSql($schemaTool) as $sql) {
            try {
                $conn->executeQuery($sql);
            } catch (\Exception $e) {
                throw ToolsException::schemaToolFailure($sql, $e);
            }
        }
    }

    /**
     * @param SchemaTool $schemaTool
     *
     * @return array
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function getSchemaSql(SchemaTool $schemaTool)
    {
        static $createSchemaSql;

        if (!is_null($createSchemaSql)) {
            return $createSchemaSql;
        }

        $createSchemaSql = [];
        $meta = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
        $indexes = [];

        foreach ($schemaTool->getCreateSchemaSql($meta) as $sql) {
            $sql = preg_replace("/enum\([^\)]*\)/is", 'varchar(255)', $sql); // NOT NULL
            $sql = preg_replace('/unsigned/is', '', $sql); // UNSIGNED
            $sql = preg_replace("/BIGINT\(\d+\)\s*/is", 'INTEGER ', $sql); // BIGINT -> INTEGER
            $sql = preg_replace("/mediumtext\s*/is", 'TEXT ', $sql); // MEDIUMTEXT -> TEXT
            $sql = preg_replace("/ INT(\(\d+\))\s*/is", ' INTEGER ', $sql); // INT -> INTEGER
            $sql = preg_replace('/ INT /is', ' INTEGER  ', $sql); // INT -> INTEGER
            $sql = preg_replace('/ON UPDATE CURRENT_TIMESTAMP/is', ' ', $sql); // remove ON UPDATE trigger
            $sql = preg_replace('/ON CREATE CURRENT_TIMESTAMP/is', ' ', $sql); // remove ON CREATE trigger

            // Replace duplicated UNIQUE INDEX
            if (preg_match("/INDEX ([a-zA-Z\d_-]+) /is", $sql, $match)) {
                if (in_array($match[1], $indexes)) {
                    $match[1] .= uniqid();
                    $sql = preg_replace("/INDEX ([a-zA-Z\d_-]+) /is", 'INDEX '.$match[1].' ', $sql);
                }
                $indexes[] = $match[1];
            }

            if (preg_match('/NOT NULL AUTO_INCREMENT/is', $sql)) {
                $sql = preg_replace('/NOT NULL AUTO_INCREMENT/is', 'PRIMARY KEY AUTOINCREMENT', $sql); // AUTO_INCREMENT
                $sql = preg_replace("/, PRIMARY KEY\(.*?\)/is", '', $sql); // REMOVE PRIMARY KEY
            } elseif (preg_match('/AUTO_INCREMENT/is', $sql)) {
                $sql = preg_replace('/AUTO_INCREMENT/is', 'PRIMARY KEY AUTOINCREMENT', $sql); // AUTO_INCREMENT
                $sql = preg_replace("/, PRIMARY KEY\(.*?\)/is", '', $sql); // REMOVE PRIMARY KEY
            }

            $createSchemaSql[] = $sql;
        }

        return $createSchemaSql;
    }

    /**
     * Returns the doctrine orm entity manager.
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->container->get($this->entityManagerServiceId);
    }

    /**
     * Returns class metadata.
     *
     * @param string $className
     *
     * @return ClassMetadata
     */
    protected function getClassMetadata($className)
    {
        return $this->getEntityManager()->getMetadataFactory()->getMetadataFor($className);
    }

    /**
     * Returns DI Container.
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @param array $fixtures
     */
    protected function loadFixtures(array $fixtures)
    {
        $loader = new DataLoader();
        $loader->setContainer($this->getContainer());

        foreach ($fixtures as $fixture) {
            if (count($fixture) == 1) {
                $fixture[] = 1;
                $fixture[] = null;
            } elseif (count($fixture) == 2) {
                $fixture[] = null;
            }

            list($fixtureClass, $count, $dir) = $fixture;
            $loader->addFixture($fixtureClass, $count ? $count : null, $dir ? $dir : null);
        }

        $loader->load($this->getEntityManager());
    }

    protected function tearDown()
    {
        parent::tearDown();

        // Decreasing memory usage
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }

        gc_collect_cycles();
    }

    /**
     * @param string $name        - array key for checking
     * @param array  $item        - array for checking
     * @param mixed  $resultValue - value that must be equal $item[$name]
     */
    protected function checkField($name, $item, $resultValue)
    {
        if (($resultValue instanceof PersistentCollection) || ($resultValue instanceof ArrayCollection)) {
            $resultValue = $resultValue->toArray();
        }

        if (($resultValue instanceof \DateTime)) {
            $resultValue = $resultValue->format(self::DATE_FORMAT);
        }

        $this->assertArrayHasKey($name, $item);
        $this->assertEquals($resultValue, $item[$name]);
    }
}
