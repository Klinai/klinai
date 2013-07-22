<?php

namespace KlinaiTest\Client;

use Klinai\Client\ClientConfig;
use Klinai\Client\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $this->config = new ClientConfig( require '_files/config.php' );
        $this->client = new Client($this->config);
    }

    public function testSetConfig()
    {
        $config = new ClientConfig( require '_files/config.php' );
        $client = new Client($this->config);

        $this->assertSame($config, $client->getConfig());
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
        $this->setExpectedException0("Klinai\Client\Exception\DocumentNotExistsException");
        $this->markTestIncomplete();
    }
}