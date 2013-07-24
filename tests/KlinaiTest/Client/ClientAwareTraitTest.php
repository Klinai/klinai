<?php

namespace KlinaiTest\Client;

use Klinai\Client\Client;
use Klinai\Client\ClientAwareTrait;

class ClientAwareTraitTest extends \PHPUnit_Framework_TestCase
{
    private $traitObject;
    private $client;
    private $r_setClient;
    private $r_getClient;


    public function setUp()
    {
        /* @var $traitObject \PHPUnit_Framework_MockObject_MockObject */
        $this->traitObject = $this->createObjectForTrait();
        $this->client = new Client();

        $this->r_setClient = new \ReflectionMethod($traitObject, 'setClient');
        $this->r_setClient->setAccessible(true);

        $this->r_getClient = new \ReflectionMethod($traitObject, 'getClient');
        $this->r_getClient->setAccessible(true);

    }

    /**
     *
     * @return \Klinai\Client\ClientAwareTrait
     */
    private function createObjectForTrait()
    {
        $traitName = 'Klinai\Client\ClientAwareTrait';

        return $this->getObjectForTrait($traitName);
    }



    public function testSetClientSame()
    {
        // must be same
        $this->r_setClient->invoke($this->traitObject,$this->client);
        $client = $this->r_getClient->invoke($this->traitObject);
        $this->assertSame($client, $this->client);
    }

    public function testSetClientToNull()
    {
        // must be null
        $this->r_setClient->invoke($this->traitObject,$this->client);
        $this->r_setClient->invoke($this->traitObject,null);

        $client = $this->r_getClient->invoke($this->traitObject);
        $this->assertNull($client);
    }



    public function testGetClientNull()
    {
        // first must be null
        $client = $this->r_getClient->invoke($this->traitObject);
        $this->assertNull($client);
    }


    public function testGetClientSame()
    {
        // secound must be same
        $this->r_setClient->invoke($this->traitObject,$this->client);
        $client = $this->r_getClient->invoke($this->traitObject);
        $this->assertSame($client, $this->client);
    }
}
