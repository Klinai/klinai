<?php

namespace Klinai\Client;

class SingleClient extends AbstractClient
{
    public function __construct($databaseName, $hostDSN)
    {
        
    }
    
    public function getDoc($docId);

    public function storeDoc(Document $doc);

    public function storeDocAsArray(array $doc);

    public function storeAttachmentForDoc($attachment);
}