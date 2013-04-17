<?php

namespace Klinai\Client;

use Zend\Http\Request;

use Zend\Http\Client as HttpClient;

abstract class AbstractClient
{
    protected $httpClient;
    protected $request;
    
    public function __construct()
    {
        $this->httpClient = new HttpClient();
        $this->initRequest ();
    }

    /**
     * 
     * @return \Zend\Http\Client
     */
    public function getHttpClient ()
    {
        return $this->httpClient;
    }

    abstract public function getDoc($databaseName,$docId);

    abstract public function storeDoc($databaseName,$doc);

    abstract public function storeAttachmentForDoc($databaseName,$doc,$attachment);

    public function sendRequest ()
    {
        $response = $this->getHttpClient()->send($this->getRequest());
        $this->initRequest();
        
        return json_decode($response->getBody());
    }
    public function initRequest ()
    {
        $this->request = new Request();
    }
    /**
     * 
     * @return \Zend\Http\Request
     */
    public function getRequest ()
    {
        return $this->request;
    }
    

}