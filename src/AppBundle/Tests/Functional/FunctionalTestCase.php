<?php

namespace AppBundle\Tests\Functional;

use AppBundle\DataFixtures\Tests\DataLoader;
use AppBundle\Tests\Integration\OrmTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Nelmio\Alice\Fixtures;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

abstract class FunctionalTestCase extends OrmTestCase
{
    const DATE_FORMAT = DATE_ISO8601;
    
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $environment = 'test_persistent';

    /**
     * @var string
     */
    protected static $sqliteFile;

    public function setUp()
    {
        parent::setUp(false);

        $this->client = null;
        $dbPath = $this->getContainer()->getParameter('sqlite_path');
        if (!file_exists($dbPath . '.tmp')) {
            if (!static::$sqliteFile) {
                $this->createSchema();
                static::$sqliteFile = file_get_contents($dbPath);
                file_put_contents($dbPath . '.tmp', static::$sqliteFile);
            } else {
                file_put_contents($dbPath, static::$sqliteFile);
            }
        } else {
            file_put_contents($dbPath, file_get_contents($dbPath . '.tmp'));
        }
    }

    /**
     */
    protected function onNotSuccessfulTest(\Exception $e)
    {
        @unlink($this->getContainer()->getParameter('sqlite_path'));
        parent::onNotSuccessfulTest($e);
    }

    /**
     * Creates new client.
     */
    protected function buildClient()
    {
        $this->client = static::createClient([
            'environment' => $this->environment,
            'debug' => $this->debug,
        ]);
    }

    /**
     * Returns client.
     *
     * @return Client
     */
    protected function getClient()
    {
        if ($this->client === null) {
            $this->buildClient();
        }

        return $this->client;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return Router
     */
    protected function getRouter()
    {
        return $this->getContainer()->get('router');
    }

    /**
     * Router route url
     *
     * @param string $routeName
     * @param array $parameters
     * @param bool|string $referenceType
     * @return string
     */
    public function generateUrl(
        $routeName,
        array $parameters = [],
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    )
    {
        return $this->getRouter()->generate($routeName, $parameters, $referenceType);
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->getClient()->getContainer();
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
            } else if (count($fixture) == 2) {
                $fixture[] = null;
            }

            list($fixtureClass, $count, $dir) = $fixture;
            $loader->addFixture($fixtureClass, $count ? $count : null, $dir ? $dir : null);
        }

        $loader->load($this->getEntityManager());
    }

    /**
     * @param string $method
     * @param string $uri 
     * @param array $data
     * @param array $parameters
     * @param array $files
     * @param array $server
     */
    protected function request(
        $method,
        $uri,
        array $data = [],
        array $parameters = [],
        array $files = [],
        array $server = []
    ) {
        $this->getClient()->request($method, $uri, $parameters, $files, $server, json_encode($data));
    }

    /**
     * @return string
     */
    protected function getResponseContent()
    {
        return $this->getClient()->getResponse()->getContent();
    }

    /**
     * @return int
     */
    protected function getResponseStatus()
    {
        return $this->getClient()->getResponse()->getStatusCode();
    }

    /**
     * @return array
     */
    protected function getResponseHeaders()
    {
        return $this->getClient()->getResponse()->headers;
    }

    /**
     * @return \Symfony\Component\BrowserKit\CookieJar
     */
    protected function getResponseCookies()
    {
        return $this->getClient()->getCookieJar();
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

    /**
     * Check that all methods except allowed denied.
     *
     * @param array  $allows     - allowed http methods
     * @param string $route      - route to check
     * @param array  $data       - content for request
     * @param array  $parameters - parameters for request
     */
    protected function checkRoute(array $allows, $route, array $data = [], array $parameters = [])
    {
        $methods = ['get', 'post', 'put', 'delete', 'patch'];
        $allows  = array_map('mb_strtolower', $allows);

        foreach ($methods as $method) {
            if (in_array($method, $allows)) {
                continue;
            }
            $this->request($method, $route, $data, $parameters);
            $this->assertEquals(405, $this->getResponseStatus());
        }
    }
}
