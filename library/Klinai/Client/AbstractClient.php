<?php

namespace Klinai\Client;

use Klinai\Client\Exception\RequestException;

use Zend\Http\Client\Adapter\Socket as HttpAdapterSocket;

use Zend\Http\Client\Adapter\Curl as HttpAdapterCurl;

use Zend\Http\Request;

use Zend\Http\Client as HttpClient;

use Zend\Http\Client\Adapter\Exception\InitializationException;
use Zend\Http\Header\ContentType;

abstract class AbstractClient
{
    use \Klinai\Client\DetectErrorReasonTrait;

    protected $httpClient;
    protected $request;
    protected $adapter;

    public function __construct()
    {
        $this->httpClient = new HttpClient();

        $this->initRequest();
        $this->initAdapter();
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

    public function sendRequest ($jsonDecode=true)
    {
        try {
            $responseContent = $this->getHttpClient()->send($this->getRequest());
            $this->initRequest();
        } catch (\RuntimeException $e) {
            throw new RequestException(sprintf("some thing was failed: %s",$e->getMessage()), null, $e);
        }

        if ( $jsonDecode ) {
            return json_decode($responseContent->getBody());
        } else {
            return $responseContent->getBody();
        }
    }
    public function setTimeout ($ms)
    {
        $httpAdapter = $this->httpClient->getAdapter();

        if ( $httpAdapter instanceof HttpAdapterCurl || $httpAdapter instanceof HttpAdapterSocket ) {
            $httpAdapter->setOptions(array('timeout' , ceil($ms / 1000) ));

            if ( $httpAdapter instanceof HttpAdapterCurl ) {
                // @todo this is a bad fix... zend currently support no Milisec only Sec
                $httpAdapter->setCurlOption(CURLOPT_CONNECTTIMEOUT_MS, $ms );
            }
            return true;
        }

        return false;
    }
    public function initRequest ()
    {
        $this->request = new Request();
        $this->request->getHeaders()->addHeaderLine('content-type','application/json');
    }
    public function initAdapter ()
    {
        $adapterClassList = array (
            'Zend\Http\Client\Adapter\Curl',
            'Zend\Http\Client\Adapter\Socket'
        );

        foreach ( $adapterClassList as $adapterClass ) {
            try {
                $this->getHttpClient()->setAdapter(new $adapterClass());
                return;
            } catch ( InitializationException $e ) {

            }
        }
        throw new \RuntimeException("no adapter class can init");
    }
    /**
     *
     * @return \Zend\Http\Request
     */
    public function getRequest ()
    {
        return $this->request;
    }
    /**
     *
     * @return \Zend\Http\Request
     */
    public function getAdapter ()
    {
        return $this->getHttpClient()->getAdapter();
    }
}
