<?php

namespace KlinaiTest\Client;

use Klinai\Client\ClientConfig;
use Klinai\Client\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    protected $client;
    protected $config;

    public function setUp()
    {
        $this->config = new ClientConfig( require './tests/_files/config.php' );
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

    public function testUpdateDoc()
    {
        $docData1 = array(
            'key1'=>'foo',
            'key2'=>'bar',
        );

        $response1 = $this->client->storeDoc('client_test1', $docData1);

        $this->assertObjectHasAttribute("id", $response1);
        $this->assertObjectHasAttribute("rev", $response1);

        $docData2 = array(
            '_id'=>$response1->id,
            '_rev'=>$response1->rev,
            'key1'=>'foo',
            'key2'=>'test',
        );
        $response2 = $this->client->storeDoc('client_test1', $docData2);

        $this->assertObjectHasAttribute("id", $response2);
        $this->assertObjectHasAttribute("rev", $response2);

        $this->assertEquals($response1->id,$response2->id);
        $this->assertNotEquals($response1->rev,$response2->rev);

        $this->assertRegExp('/2-(.+)/',$response2->rev);

    }

    public function testGetDocument()
    {
        $docData = array(
            'key1'=>'foo',
            'key2'=>'bar',
        );

        $response = $this->client->storeDoc('client_test1', $docData);

        $doc = $this->client->getDoc('client_test1', $response->id);
        $fields = $doc->getFields();
        $this->assertObjectHasAttribute("key1", $fields);
        $this->assertObjectHasAttribute("key2", $fields);
    }

    public function testDatabaseIsNotExists()
    {
        $this->setExpectedException("Klinai\Client\Exception\DatabaseNotExistsException");


        $response = $this->client->getDoc('not_exists_database', 'some_doc_id');
    }

    public function testDocumentIsNotExists()
    {
        $this->setExpectedException("Klinai\Client\Exception\DocumentNotExistsException");

        $response = $this->client->getDoc('client_test1', 'not_exists_document_id');
    }

    public function testDeleteDocument()
    {
        $this->setExpectedException("Klinai\Client\Exception\DocumentNotExistsException");


        $response = $this->client->storeDoc('client_test1', array(
            'foo'=>'dummy',
            'bar'=>'dummy',
        ));

        $doc = $this->client->getDoc('client_test1', $response->id);

        $this->assertObjectHasAttribute('_id',$doc->getFields());
        $this->assertObjectHasAttribute('_rev',$doc->getFields());

        $this->client->deleteDocument('client_test1', $doc);

        $this->assertNull($doc);
        $this->client->getDoc('client_test1', $response->id);
    }
}