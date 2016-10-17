<?php

namespace SGQL\Lib\Config;

class DatabaseTest extends \PHPUnit_Framework_TestCase {
    private $basicHosts = [
        'login' => [
            'host' => 'localhost',
            'username' => 'login',
            'password' => 'sgql',
            'charset' => 'utf8',
            'dbRegex' => 'login',
        ],
        'data' => [
            'host' => 'localhost',
            'username' => 'data',
            'password' => 'sgql',
            'charset' => 'utf8',
            'dbRegex' => 'data_[1|2]',
        ],
    ];

    private $duplicateHosts = [
        'data_a' => [
            'host' => '192.168.0.1',
            'username' => 'data',
            'password' => 'sgql',
            'charset' => 'utf8',
            'dbRegex' => 'data_[1|2]',
        ],
        'data_b' => [
            'host' => '192.168.0.2',
            'username' => 'data',
            'password' => 'sgql',
            'charset' => 'utf8',
            'dbRegex' => 'data_[1|2]',
        ],
    ];

    public function testDatabase() {
        $database = new Database($this->basicHosts);

        $host = $database->getHost('login');
        $this->assertEquals($host->getId(), 'login');

        $host = $database->getHost('data_1');
        $this->assertEquals($host->getId(), 'data');

        $host = $database->getHost('data_2');
        $this->assertEquals($host->getId(), 'data');
    }

    public function testPickRandomHost() {
        $database = new Database($this->duplicateHosts);

        $hosts = ['data_a', 'data_b'];

        $host = $database->getHost('data_1');
        unset($hosts[array_search($host->getId(), $hosts)]);

        $loops = 15;
        for ($i = 0; ; $i++) {
            $host = $database->getHost('data_2');

            if (in_array($host->getId(), $hosts)) {
                break;
            } else if ($i == $loops) {
                $this->markTestSkipped("Re-run test, random number generator wasn't random enough");
            }
        }
    }

    public function testNonexistantDatabase() {
        $database = new Database($this->basicHosts);

        try {
            $database->getHost('data_3');
            $this->fail("Expected exception for no hosts having access to this database");
        } catch (\Exception $e) {
            $this->assertEquals("No hosts have access to database 'data_3'", $e->getMessage());
        }
    }

    public function testCantConnect() {
        $database = new Database($this->duplicateHosts);
        $cantConnectHost = 'data_b';

        $database->cantConnect($cantConnectHost);

        // Get host for database data_1 many times to make sure host data_b is never returned
        $loops = 15;
        for ($i = 0; ; $i++) {
            $host = $database->getHost('data_2');

            if ($host->getId() === $cantConnectHost) {
                $this->fail("Host was returned that was indicated unconnectable");
            } else if ($i == $loops) {
                return;
            }
        }
    }
}
