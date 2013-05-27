<?php

namespace Klinai\Model;

class Attachment implements AttachmentInterface
{
    protected $fields;
    protected $contentPuffer;
    protected $rootDocument;

    public function __construct($id,$data,$rootDocument) {

    }

    public function getId() {
        return $this->id;
    }
    public function getContent() {
        if ( $this->$contentPuffer != null ) {
            return $this->contentPuffer;
        }

        return $contentPuffer;
    }

    public function loadContentIntoPuffer () {
        if ( $this->$contentPuffer != null ) {
            return $this->contentPuffer;
        }

        return $contentPuffer;
    }
}