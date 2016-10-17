<?php

namespace SGQL;

include_once(dirname(__FILE__).'/database/mysql_config.php');

class MySQL_Database_TestCase extends \PHPUnit_Framework_TestCase {
    private static $rootUser;
    private static $unittestDatabases = ['login', 'data_1', 'data_2'];
    protected $fixture = [];
    private static $haltFixtureSetup = false;

    protected static $hosts = [
        [
            'host' => 'localhost',
            'username' => 'sgql_test_login',
            'password' => 'sgql',
            'charset' => 'utf8',
            'dbRegex' => 'sgql_unittests_login',
            'databases' => ['sgql_unittests_login'] // Only used to set up user for tests
        ],
        [
            'host' => 'localhost',
            'username' => 'sgql_test_data',
            'password' => 'sgql',
            'charset' => 'utf8',
            'dbRegex' => 'sgql_unittests_data_[1|2]',
            'databases' => ['sgql_unittests_data_1', 'sgql_unittests_data_2'], // Only used to set up user for tests
        ],
    ];

    public function setUp() {
        parent::setUp();

        if (is_null(self::$rootUser)) {
            $this->markTestSkipped("Unable to make database connection");
        } else if (isset($this->fixture[0]) && ($this->fixture[1] === true || !self::$haltFixtureSetup)) {
            // Load fixture
            $this->loadFixtures($this->fixture[0]);

            if ($this->fixture[1] !== true) {
                // Fixture file should only be loaded once for this file
                self::$haltFixtureSetup = true;
            }
        }
    }

    public function tearDown() {
        parent::tearDown();

        if (is_null(self::$rootUser)) {
            return;
        } else if (isset($this->fixture[1]) && $this->fixture[1] === true) {
            // Destroy the fixture after each test
            foreach (self::$unittestDatabases as $database) {
                self::$rootUser->exec("DROP DATABASE sgql_unittests_".$database."; CREATE DATABASE sgql_unittests_".$database.";");
            }
        }
    }

    public static function setUpBeforeClass() {
        global $mysqlConfig;

        $dsn = 'mysql:host='.$mysqlConfig['host'].';charset=utf8';
        $opt = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$rootUser = new \PDO($dsn, $mysqlConfig['username'], $mysqlConfig['password'], $opt);
        } catch (\Exception $e) {
            self::$rootUser = null;
            return;
            // Handled later before each test by marking the test as incomplete because the db connection hasn't been made
        }

        self::dbSetup();
    }

    public static function tearDownAfterClass() {
        self::dbCleanup();
        self::$rootUser = null;
        self::$haltFixtureSetup = false;
    }

    public function loadFixtures($fixtures = []) {
        if (!is_array($fixtures)) {
            return;
        }

        foreach ($fixtures as $fixture) {
            $result = self::$rootUser->exec(file_get_contents(dirname(__FILE__).'/database/fixtures/'.$fixture));
        }
    }

    public static function dbSetup() {
        if (is_null(self::$rootUser)) {
            return;
        }

        self::dbCleanup();
        foreach (self::$unittestDatabases as $database) {
            self::$rootUser->exec("CREATE DATABASE sgql_unittests_".$database);
        }

        foreach (self::$hosts as $host) {
            self::$rootUser->exec("CREATE USER '".$host['username']."'@'%' IDENTIFIED BY '".$host['password']."';");

            foreach ($host['databases'] as $database) {
                self::$rootUser->exec("GRANT ALL PRIVILEGES ON ".$database.".* TO '".$host['username']."'@'%' IDENTIFIED BY '".$host['password']."';");
            }
        }
    }

    public static function dbCleanup() {
        if (is_null(self::$rootUser)) {
            return;
        }

        foreach (self::$unittestDatabases as $database) {
            self::$rootUser->exec("DROP DATABASE IF EXISTS sgql_unittests_".$database);
        }

        foreach (self::$hosts as $host) {
            self::$rootUser->exec("DROP USER IF EXISTS '".$host['username']."'@'%'");
        }
    }
}
