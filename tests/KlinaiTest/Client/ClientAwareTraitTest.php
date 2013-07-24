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
        $this->traitObject = $this->createObjectForTrait();
        $this->client = new Client();

        // we need an property reflection because we dont want the setter or getter to controll the value
        $this->r_client_property = new \ReflectionProperty($this->traitObject, 'couchClient');
        $this->r_client_property->setAccessible(true);

        $this->r_setClient = new \ReflectionMethod($this->traitObject, 'setClient');
        $this->r_setClient->setAccessible(true);

        $this->r_getClient = new \ReflectionMethod($this->traitObject, 'getClient');
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

        $client = $this->r_client_property->getValue($this->traitObject);
        $this->assertSame($client, $this->client);
    }

    public function testSetClientToNull()
    {
        // must be null
        $this->r_client_property->setValue($this->traitObject, null);

        $client = $this->r_client_property->getValue($this->traitObject);
        $this->assertNull($client);
    }



    public function testGetClientNull()
    {
        // first must be null
        $this->r_client_property->setValue($this->traitObject, null);

        $client = $this->r_getClient->invoke($this->traitObject);
        $this->assertNull($client);
    }


    public function testGetClientSame()
    {
        // secound must be same
        // first must be null
        $this->r_client_property->setValue($this->traitObject, $this->client);

        $client = $this->r_getClient->invoke($this->traitObject);
        $this->assertSame($client, $this->client);
    }
}
