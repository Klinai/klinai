<?php

namespace Klinai\Model;

use Klinai\Client\AbstractClient;
use Klinai\Client\ClientAwareTrait;
class Attachment implements AttachmentInterface
{
    use ClientAwareTrait;

    protected $id;
    protected $fields;
    protected $contentPuffer;
    protected $rootDocument;
    protected $pufferContentEnabled;

    public function __construct($id,$data, Document $rootDocument,AbstractClient $couchClient) {
        $this->id= $id;
        $this->fields = $data;
        $this->enableContentPuffer();
        $this->rootDocument = $rootDocument;
        $this->setClient($couchClient);
    }

    public function getId() {
        return $this->id;
    }
    public function enableContentPuffer() {
        return $this->pufferContentEnabled = true;
    }
    public function disableContentPuffer() {
        return $this->pufferContentEnabled = false;
    }
    public function isContentPufferEnabled() {
        return $this->pufferContentEnabled === true;
    }
    public function getContent() {
        if ( $this->hasPufferContent() ) {
            return $this->contentPuffer;
        }

        if ( !$this->isContentPufferEnabled() ) {
            return $this->loadContent();
        }

        $this->pufferContent();
        return $this->contentPuffer;
    }

    protected function loadContent () {

        $client = $this->getClient();
        return $client->getAttachmentContent($this->rootDocument->getSourceDatabase(),
                                             $this->rootDocument->get('_id'),
                                             $this->getId()
        );
    }

    public function pufferContent () {
        if ( $this->hasPufferContent() ) {
            return $this->contentPuffer;
        }
        $this->contentPuffer = $this->loadContent();
    }

    public function hasPufferContent () {
        return $this->contentPuffer != null;
    }
}