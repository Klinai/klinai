<?php

namespace Klinai\Client;

class AbstractClient
{
    public function getDoc($docId);

    public function storeDoc(Document $doc);

    public function storeDocAsArray(array $doc);

    public function storeAttachmentForDoc($attachment);
    
    public function request ($host,$path,$methode,$data)
    {

    }
    

}