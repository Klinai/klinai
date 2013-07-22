<?php

namespace KlinaiTest\Client;

use Klinai\Client\ClientConfig;
use Klinai\Client\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $client;
    protected $config;

    public function setUpBeforeClass()
    {
        require '_files/initDefaultDatabase.php';
    }
    public function setUp()
    {
        $this->config = new ClientConfig( require '_files/config.php' );
        $this->client = new Client();
        $this->client->setConfig($this->config);
    }

    public function testSetConfig()
    {
        $client = new Client();
        $client->setConfig($this->config);
        $this->assertSame($this->config, $client->getConfig());
    }

    public function testStoreDoc()
    {
        $docData = array(
            'key1'=>'foo',
            'key2'=>'bar',
        );

        $response = $this->client->storeDoc('client_test1', $docData);

        $this->assertObjectHasAttribute("id", $response);
        $this->assertObjectHasAttribute("rev", $response);

    }

    public function testGetDoc()
    {
        $this->markTestIncomplete();
    }

    public function testDatabaseIsNotExists()
    {
        $this->setExpectedException("Klinai\Client\Exception\DatabaseNotExistsException");
        $this->markTestIncomplete();
    }

    public function testDocumentIsNotExists()
    {
        $this->setExpectedException("Klinai\Client\Exception\DocumentNotExistsException");
        $this->markTestIncomplete();
    }
}