<?php

namespace Klinai\Client;

use Klinai\Model\Document;

use Zend\Http\Request;

class Client extends AbstractClient
{
    protected $config;

    public function setConfig ( ClientConfig $config)
    {
        $this->config = $config;
    }

    /**
     * 
     * @return ClientConfig
     */
    public function getConfig ()
    {
        return $this->config;
    }

    public function getDoc($databaseName,$docId)
    {
        $uri = $this->buildUri($databaseName,$docId,$this->getRequestParameters());

        $request = $this->getRequest();
        $request->setUri($uri);
        $request->setMethod($request::METHOD_GET);

        $response = $this->sendRequest();

        return new Document($response, $this, $databaseName);
    }

    public function storeDoc($databaseName,$doc)
    {
        if ( !$doc instanceof Document && !$doc instanceof \stdClass && !is_array($doc)) {
            throw new RuntimeException("doc is not a instance of (Document or stdClass or Array)");
        }

        $uri = $this->buildUri($databaseName,$doc->get('_id'),$this->getRequestParameters());
        
        $request = $this->getRequest();
        $request->setUri($uri);
        $request->setMethod($request::METHOD_PUT);
        $request->setContent($doc->toJson());

        $response = $this->sendRequest();
        return $response;
    }

    public function storeAttachmentForDoc($attachment, $doc,$databaseName)
    {
        throw new \Exception("currently not ready");
    }

    public function buildUri($database,$docId,$parameters)
    {
        $databaseData = $this->getConfig()->getDataForIndex($database);
        
        return implode ('/',array($databaseData['host'],$database,$docId)).
               '?'.http_build_query($parameters);
    }

    public function getDatabaseNameFromFullId($docId)
    {
        preg_match('#^(?<database>[^/]+)/((?<idPrefix>_[^/]+)?(?<id>[^/]+))',$docId,$matches);
        
        return $matches['database'];
    }

    /**
     * 
     * @return multitype:
     */
    public function getRequestParameters()
    {
        return array();
    }

    /**
     * 
     * @return multitype:
     */
    public function parseFQID($id)
    {
        throw new \Exception("currently not ready");
    }
}