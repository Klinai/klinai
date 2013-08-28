<?php

namespace KlinaiTest\Client;

use Klinai\Client\ClientConfig;

class ClientConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $configArray;

    public function setUp()
    {
        $this->configArray = require './tests/_files/config.php';
    }

    public function testCreateClientConfig()
    {
        $config1 = new ClientConfig($this->configArray);

        $config2 = new ClientConfig();
        $config2->setConfig($this->configArray);

        // check database keys
        $databaseKeys = array_keys($this->configArray['databases']);
        $this->assertEquals($config1->getAllDatabase(),
                            $databaseKeys);

        $this->assertEquals($config2->getAllDatabase(),
                            $databaseKeys);

    }

    public function testValidateConfigIfSuccess()
    {
        $config = new ClientConfig();
        $this->assertTrue($config->validateConfig($this->configArray) );
    }

    public function testValidateConfigIfFaild()
    {
        $this->markTestSkipped('this function is currently not faild');

        $this->setExpectedException("Klinai\Client\Exception\ConfigIsNotValidException");

        $config = new ClientConfig();
        $config->validateConfig(array());
    }

    public function testIsConfigValidIfSuccess()
    {

        $config = new ClientConfig();
        $this->assertTrue( $config->isConfigValid($this->configArray) );
    }

    public function testIsConfigValidIfFaild()
    {
        $this->markTestSkipped('this function is currently not faild');

        $config = new ClientConfig();
        $this->assertFalse( $config->isConfigValid(array()) );
    }

    public function testIsConfigValidIfFaildThrow()
    {
        $this->markTestSkipped('this function is currently not faild');

        $this->setExpectedException("Klinai\Client\Exception\ConfigIsNotValidException");

        $config = new ClientConfig();
        $config->isConfigValid(array(),true);
    }


    public function testGetConfigData()
    {
        $config = new ClientConfig($this->configArray);

        $key = 'client_test1';
        $data = $config->getDataForIndex($key);

        $this->assertArrayHasKey( 'dbname', $data) ;
        $this->assertArrayHasKey( 'host', $data) ;
        $this->assertEquals( $data, $this->configArray['databases'][$key]) ;

        $key = 'client_test2';
        $data = $config->getDataForIndex($key);

        $this->assertArrayHasKey( 'dbname', $data) ;
        $this->assertArrayHasKey( 'host', $data) ;
        $this->assertEquals( $data, $this->configArray['databases'][$key]) ;
    }

    public function testNoExistDatabase()
    {
        $config = new ClientConfig($this->configArray);

        $this->assertFalse($config->hasDatabase("notExistsDatabaseIndex"));
    }

    /**
     * @covers \Klinai\Client\ClientConfig::getDataForIndex
     */
    public function testGetNoExistDatabase()
    {
        $this->setExpectedException("Klinai\Client\Exception\DatabaseIndexIsNotExistsException");

        $config = new ClientConfig($this->configArray);
        $data = $config->getDataForIndex("notExistsDatabaseIndex");
    }
}