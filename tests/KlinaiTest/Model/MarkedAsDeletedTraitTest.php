<?php

namespace KlinaiTest\Model;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class MarkedAsDeletedTraitTest extends PHPUnit_Framework_TestCase
{
    protected $traitObject;
    protected $rp_deleted;
    protected $rm_setDeleted;
    protected $rm_isDeleted;
    protected $rm_checkDeleteForDoSomething;

    public function setUp ()
    {
        $this->traitObject = $this->createObjectForTrait();

        // we need an property reflection because we dont want the setter or getter to controll the value
        $this->rp_deleted = new \ReflectionProperty($this->traitObject, 'deleted');
        $this->rp_deleted->setAccessible(true);

        $this->rm_setDeleted = new \ReflectionMethod($this->traitObject, 'setDeleted');
        $this->rm_setDeleted->setAccessible(true);

        $this->rm_isDeleted = new \ReflectionMethod($this->traitObject, 'isDeleted');
        $this->rm_isDeleted->setAccessible(true);

        $this->rm_checkDeleteForDoSomething = new \ReflectionMethod($this->traitObject, 'checkDeleteForDoSomething');
        $this->rm_checkDeleteForDoSomething->setAccessible(true);
    }

    /**
     *
     * @return \Klinai\Model\MarkedAsDeletedTrait
     */
    private function createObjectForTrait()
    {
        $traitName = 'Klinai\Model\MarkedAsDeletedTrait';

        return $this->getObjectForTrait($traitName);
    }

    public function testIsDeleted ()
    {
        $this->assertFalse($this->rm_isDeleted->invoke($this->traitObject));

        // set to deleted
        $this->rp_deleted->setValue($this->traitObject,true);

        $this->assertTrue($this->rm_isDeleted->invoke($this->traitObject));
    }

    public function testSetDeleted ()
    {
        $this->assertFalse($this->rp_deleted->getValue($this->traitObject));

        // set to deleted
        $this->rm_setDeleted->invoke($this->traitObject);

        $this->assertTrue($this->rp_deleted->getValue($this->traitObject));
    }

    public function testCheckDeleteForDoSomethingFailed ()
    {
        $this->setExpectedException('Klinai\Model\Exception\MarkedAsDeletedException');

        $this->rp_deleted->setValue($this->traitObject,true);

        $this->rm_checkDeleteForDoSomething->invoke($this->traitObject);
    }

    public function testCheckDeleteForDoSomethingSuccess ()
    {
        $this->assertFalse($this->rp_deleted->getValue($this->traitObject));

        $this->rm_checkDeleteForDoSomething->invoke($this->traitObject);
    }
}
