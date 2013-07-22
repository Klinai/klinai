<?php

namespace KlinaiTest\Client;

use Klinai\Client\ClientConfig;

class ClientConfigTest extends \PHPUnit_Framework_TestCase
{
    protected $configArray;

    public function setUpBeforeClass()
    {
        $this->configArray = require_once '_files/config.php';
    }

    public function testCreateClientConfig()
    {
        $config1 = new ClientConfig($this->configArray);

        $config2 = new ClientConfig();
        $config2->setConfig($this->configArray);

        // check database keys
        $this->assertEquals($config1->getAllDatabase(),
                            array_keys($this->configArray));

        $this->assertEquals($config2->getAllDatabase(),
                            array_keys($this->configArray));

    }

    public function testValidConfig()
    {
        $this->setExpectedException("Klinai\Client\Exception\ConfigIsNotValidException");

        $this->markTestIncomplete("valid config");
    }

    public function testGetConfigData()
    {
        $config = new ClientConfig($this->configArray);

        $config->getDatabaseIndex($databaseIndex);
    }

    public function testNoExistDatabase()
    {
        $config = new ClientConfig($this->configArray);

        $this->assertFalse($config->hasDatabase("notExistsDatabase"));
    }

    public function testGetNoExistDatabase()
    {
        $this->setExpectedException("Klinai\Client\Exception\DatabaseIndexIsNotExistsException");

        $config = new ClientConfig($this->configArray);
        $data = $config->getDataForIndex("notExistsDatabase");
    }
}