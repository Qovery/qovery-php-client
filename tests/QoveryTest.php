<?php

use PHPUnit\Framework\TestCase;

// https://phpunit.de/manual/5.7/en
final class QoveryTest extends TestCase
{
    protected static $b64;
    const LOCALCONFIGURATIONFILEPATH = "./tests/local_configuration.json";

    public static function setUpBeforeClass()
    {
        self::$b64 = file_get_contents('./tests/b64.txt');
    }

    public function setUp()
    {
        putenv("QOVERY_JSON_B64=" . self::$b64);
        putenv("QOVERY_BRANCH_NAME=master");
        putenv("QOVERY_IS_PRODUCTION=true");
    }

    public function testGetCurrentBranchName(): void
    {
        $this->assertEquals('master', (new Qovery)->getBranchName());
    }

    public function testIsProduction(): void
    {
        $this->assertEquals(true, (new Qovery)->isProduction());
    }

    public function testLoadConfigurationFile(): void
    {
        putenv("QOVERY_JSON_B64=");
        $q = new Qovery(self::LOCALCONFIGURATIONFILEPATH);
        $this->assertEquals('phpunit-disk', $q->configuration['qovery_configuration_source']);
    }

    public function testLoadBadConfigurationFile(): void
    {
        putenv("QOVERY_JSON_B64=");
        $q = new Qovery(self::LOCALCONFIGURATIONFILEPATH . ".missingFile");
        $this->assertEquals(null, $q->configuration);
    }

    public function testListDatabases(): void
    {
        $q = new Qovery();
        $dbs = $q->databasesConfiguration;
        $this->assertEquals(1, count($dbs));
        $db0 = $dbs[0];
        $this->assertInstanceOf(DatabaseConfiguration::class, $db0);
        $this->assertEquals("my-pql", $db0->name);
    }

    public function testGetExistingDatabaseByName(): void
    {
        $db = (new Qovery())->getDatabaseByName("my-pql");
        $this->assertTrue(isset($db));
        $this->assertEquals("my-pql", $db->name);
    }

    public function testGetUnknownDatabaseByName(): void
    {
        $db = (new Qovery())->getDatabaseByName("some-unknown-db");
        $this->assertEquals(null, $db);
    }

    public function testGetDatabaseHost(): void
    {
        $db = (new Qovery())->getDatabaseByName("my-pql");
        $this->assertTrue(!empty($db->host));
    }
}
