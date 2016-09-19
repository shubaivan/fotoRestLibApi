<?php

namespace AppBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

abstract class AbstractTest extends WebTestCase
{
    protected $client = null;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em;

    public function setUp()
    {
        $this->client = static::createClient();
        $kernel = static::createKernel();
        $kernel->boot();
        $this->_em = $kernel->getContainer()
            ->get('doctrine.orm.entity_manager');
    }

    public function testLoadUserByUsername()
    {
        $user = $this->_em
            ->getRepository('ArtelProfileBundle:Users')
            ->loadUserByTest('admin-admin')
        ;
        $assert[0] = $user;
        $this->assertCount(1, $assert);

        return $user;
    }

    // Create a new client with admin role to browse the application
    public function logIn()
    {
        $session = $this->client->getContainer()->get('session');
        $user = $this->testLoadUserByUsername();
        $firewall = 'default';
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
