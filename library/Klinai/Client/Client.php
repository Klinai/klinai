<?php

namespace Klinai\Client;

class Client extends AbstractClient
{
    protected $config;
    
    public function setConfig ( ClientConfig $config)
    {
        $this->config = $config;
    }

    public function getDoc($docId,$databaseName)
    {
        
    }

    public function storeDoc(Document $doc,$databaseName)
    {
        
    }

    public function storeDocAsArray(array $doc,$databaseName)
    {
        
    }

    public function storeAttachmentForDoc($attachment,$databaseName)
    {
        
    }
}