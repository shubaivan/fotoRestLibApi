<?php
namespace AppBundle\Tests\Stubs\Doctrine\DBAL;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;

class PostConnectEventSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }

    /**
     * @param ConnectionEventArgs $args
     */
    public function postConnect(ConnectionEventArgs $args)
    {
        $args->getConnection()
            ->getWrappedConnection()
            ->sqliteCreateFunction('MD5', function ($string) {
                return md5($string);
            }, 1);

        $args->getConnection()
            ->getWrappedConnection()
            ->sqliteCreateFunction('CONCAT', function () {
                return join('', func_get_args());
            });
    }
}