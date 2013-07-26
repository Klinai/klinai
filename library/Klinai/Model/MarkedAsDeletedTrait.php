<?php

namespace Klinai\Model;

trait MarkedAsDeletedTrait {

    private $deleted;

    protected function setDeleted()
    {
        $this->deleted = true;
    }

    protected function isDeleted()
    {
        return $this->deleted;
    }
    /**
     *
     * @throws MarkedAsDeletedException
     */
    protected function checkDeleteForDoSomething()
    {
        if ( $this->isDeleted() ) {
            throw new MarkedAsDeletedException("this object was marked as delete");
        }
    }
}