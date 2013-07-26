<?php

namespace KlinaiTest\Model;

use \PHPUnit_Framework_TestCase;
use Klinai\Model\Document;
use Klinai\Client\ClientConfig;

class DocumentTest extends PHPUnit_Framework_TestCase
{
    protected $mockClient;

    public function setUp()
    {
        $this->configArray = require './tests/_files/config.php';
        $this->mockClient = $this->getMock('Klinai\Client\Client');
        $this->mockClient->setConfig(new ClientConfig($this->configArray));
    }
    public function testSetSourceDatabase()
    {
        $data=array(
            'foo'=>'bar',
            'boo'=>'bar',
        );
        $sourceDatabase = 'client_test2';

        $doc = new Document($data, $this->mockClient,'client_test1');
        $doc->setSourceDatabase($sourceDatabase);

        $this->assertEquals($doc->getSourceDatabase(), $sourceDatabase);

    }
    public function testCreateDocument()
    {
        $mockReturn = (object) array (
            'id'=>'fooBar',
            'rev'=>'1-4861618161879616'
        );

        $this->mockClient = $this->getMock('Klinai\Client\Client');
        $this->mockClient->expects($this->once())
                         ->method('storeDoc')
                         ->will($this->returnValue($mockReturn));

        $data=array(
            'foo'=>'bar',
            'boo'=>'bar',
        );

        $doc = new Document($data, $this->mockClient,'client_test1');
        $doc->record();

        $this->assertEquals($mockReturn->id, $doc->get('_id'));
        $this->assertEquals($mockReturn->rev, $doc->get('_rev'));
    }
    public function testUpdateDocument()
    {
        $firstData = (object) array (
            '_id'=>'fooBar',
            '_rev'=>'1-3491449E1G4S91S648S7E49FE',
            'foo'=>'bar',
            'boo'=>'bar',
        );
        $secoundData = (object) array (
            'foo'=>'dummy',
            'boo'=>'dummy',
        );

        $mockReturn = (object) array (
            'ok'=> true,
            'id'=>$firstData->_id,
            'rev'=>'2-3491449E1F89EF1E6F87E49FE',
        );

        $this->mockClient = $this->getMock('Klinai\Client\Client');
        $this->mockClient->expects($this->any())
                         ->method('storeDoc')
                         ->will($this->returnValue($mockReturn));

        $doc = new Document($firstData, $this->mockClient, 'client_test1');
        $doc->disableAutoRecord();

        $doc->foo = $secoundData->foo;
        $doc->boo = $secoundData->boo;

        $this->assertEquals($doc->_id, $firstData->_id);
        $this->assertEquals($doc->_rev, $firstData->_rev);
        $this->assertEquals($doc->foo, $secoundData->foo);
        $this->assertEquals($doc->boo, $secoundData->boo);

        $doc->record();

        $this->assertEquals($doc->_id, $firstData->_id);
        $this->assertNotEquals($doc->_rev, $firstData->_rev);
        $this->assertEquals($doc->_rev, $mockReturn->rev);
        $this->assertEquals($doc->foo, $secoundData->foo);
        $this->assertEquals($doc->boo, $secoundData->boo);
    }
    public function testDeleteDocument()
    {
        $this->markTestIncomplete();
    }

    public function testIsAttachmentExists()
    {
        $docData = json_decode('{
           "_id": "fooBar",
           "_rev": "1-7182e53ce7cd148307e40521e9ede288",
           "foo" : "dummy",
           "boo" : "dummy",
           "_attachments": {
               "attachment1": {
                   "content_type": "text/plain",
                   "revpos": 11,
                   "digest": "md5-ezhD8uRb10w4JpIqaH8R+A==",
                   "length": 125664,
                   "stub": true
               }
           }
        }',false);

        /* @var $doc \Klinai\Model\Document */
        $doc = new Document($docData,$this->mockClient,'client_test1' );

        $this->assertTrue($doc->isAttachmentExists('attachment1'));
        $this->assertFalse($doc->isAttachmentExists('notExists'));
    }
    public function testGetAttachment()
    {
        $docData = json_decode('{
           "_id": "fooBar",
           "_rev": "1-7182e53ce7cd148307e40521e9ede288",
           "foo" : "dummy",
           "boo" : "dummy",
           "_attachments": {
               "attachment1": {
                   "content_type": "text/plain",
                   "revpos": 11,
                   "digest": "md5-ezhD8uRb10w4JpIqaH8R+A==",
                   "length": 125664,
                   "stub": true
               }
           }
        }',false);

        /* @var $doc \Klinai\Model\Document */
        $doc = new Document($docData,$this->mockClient,'client_test1' );

        $attachment = $doc->getAttachment('attachment1');

        $this->assertInstanceOf('Klinai\Model\Attachment',$attachment);
    }

    public function testGetAttachmentFaild()
    {
        $this->setExpectedException('Klinai\Model\Exception\AttachmentIsNotExistsException');

        $docData = json_decode('{
           "_id": "fooBar",
           "_rev": "1-7182e53ce7cd148307e40521e9ede288",
           "foo" : "dummy",
           "boo" : "dummy",
           "_attachments": {
               "attachment1": {
                   "content_type": "text/plain",
                   "revpos": 11,
                   "digest": "md5-ezhD8uRb10w4JpIqaH8R+A==",
                   "length": 125664,
                   "stub": true
               }
           }
        }',false);

        /* @var $doc \Klinai\Model\Document */
        $doc = new Document($docData,$this->mockClient,'client_test1' );
        $attachment = $doc->getAttachment('notExistsAttachment');
    }
}
