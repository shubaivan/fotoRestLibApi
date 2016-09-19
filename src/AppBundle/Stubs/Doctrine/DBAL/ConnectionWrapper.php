<?php
namespace AppBundle\Tests\Stubs\Doctrine\DBAL;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Connection as NativeConnection;
use Doctrine\DBAL\Driver;

class ConnectionWrapper extends NativeConnection
{
    /**
     * @param string $statement
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    public function prepare($statement)
    {
        $exps    = [];
        $uniqueId  = md5(uniqid());
        $counter = 0;

        while (preg_match("/\(([^\(\)*]+)\)/is", $statement, $matches) === 1 && isset($matches[1])) {
            $counter ++;
            $id = $uniqueId . $counter;
            $exps[$id] = $matches[1];
            $statement = preg_replace("/\([^\(\)*]+\)/is", $id, $statement, 1);
        }

        foreach (array_reverse($exps) as $id => $exp) {
            $statement = str_replace($id, '(' . $exp . ')', $statement);
            $statement = $this->checkSql($statement);
        }

        return parent::prepare($statement);
    }

    /**
     * Replace mysql functions to sqlite functions
     *
     * @param string $statement
     * @return mixed
     */
    private function checkSql($statement)
    {
        $statement = preg_replace([
            "/UNIX_TIMESTAMP\(([^\)]+)\)/is",
            "/TIMESTAMPDIFF\(SECOND, ([^,]+), ([^\)]+)\)/is",
            "/SEC_TO_TIME\(([^\)]+)\)/is",
        ], [
            "strftime('%s', $1)",
            "(strftime('%s', $2) - strftime('%s', $1))",
            "cast(round($1/3600) as integer) || ':' || cast(round(strftime('%M:%S', $1/86400.0 - 0.5)) as integer)",
        ], $statement);

        return $statement;
    }

    /**
     * Method update SQLite with mySQL functions
     */
    private function createMySQLFunctions()
    {
        $this
            ->getWrappedConnection()
            ->sqliteCreateFunction('MD5', function ($string) {
                return md5($string);
            }, 1);

        $this
            ->getWrappedConnection()
            ->sqliteCreateFunction('CONCAT', function () {
                return join('', func_get_args());
            });
    }
}
