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
    public function testGetDocument()
    {
        $mockReturn = array(
            'fooBar'=> (object) array (
                'id'=>'fooBar',
                'rev'=>'1-4861618161879616',
                'foo'=>'bar',
                'boo'=>'bar',
            ),
            'barfoo'=> (object) array (
                'id'=>'barfoo',
                'rev'=>'2-4861618161879616',
                'foo'=>'dummy',
                'boo'=>'dummy',
            )
        );

        $this->mockClient = $this->getMock('Klinai\Client\Client');
        $this->mockClient->expects($this->once())
                         ->method('getDoc')
                         ->with($this->equalTo('client_test1'),
                                $this->equalTo('fooBar'))
                         ->will($this->returnValue($mockReturn['fooBar']));

        $this->mockClient->expects($this->once())
                         ->method('getDoc')
                         ->with($this->equalTo('client_test1'),
                                $this->equalTo('barfoo'))
                         ->will($this->returnValue($mockReturn['barfoo']));

        $doc1 = $this->mockClient->getDoc ( 'client_test1', 'fooBar' );
        $doc2 = $this->mockClient->getDoc ( 'client_test1', 'barfoo' );

        $this->assertEquals($mockReturn['fooBar']->id,  $doc1->get('_id'));
        $this->assertEquals($mockReturn['fooBar']->rev, $doc1->get('_rev'));
        $this->assertEquals($mockReturn['fooBar']->foo, $doc1->get('foo'));
        $this->assertEquals($mockReturn['fooBar']->boo, $doc1->get('boo'));

        $this->assertEquals($mockReturn['barfoo']->id,  $doc2->get('_id'));
        $this->assertEquals($mockReturn['barfoo']->rev, $doc2->get('_rev'));
        $this->assertEquals($mockReturn['barfoo']->foo, $doc2->get('foo'));
        $this->assertEquals($mockReturn['barfoo']->boo, $doc2->get('boo'));
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

        $this->assertInstanceOf($attachment, 'Klinai\Model\Attchment');
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
