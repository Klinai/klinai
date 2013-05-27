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
        $uriOptions = array(
            'database'=>$databaseName,
            'docId'=>$docId,
            'parameters'=>$this->getRequestParameters()
        );
        $uri = $this->buildUri($uriOptions);

        $request = $this->getRequest();
        $request->setUri($uri);
        $request->setMethod($request::METHOD_GET);

        $response = $this->sendRequest();

        if ( isset($response->error) ) {
            throw $this->createExceptionInstance($response, $uriOptions);
        }
        return new Document($response, $this, $databaseName);
    }

    public function storeDoc($databaseName,$doc)
    {
        if ( !$doc instanceof Document && !$doc instanceof \stdClass && !is_array($doc)) {
            throw new RuntimeException("doc is not a instance of (Document or stdClass or Array)");
        }

        $uri = $this->buildUri(array(
            'database'=>$databaseName,
            'docId'=>$doc->get('_id'),
            'parameters'=>$this->getRequestParameters()
        ));

        $request = $this->getRequest();
        $request->setUri($uri);
        $request->setMethod($request::METHOD_PUT);
        $request->setContent($doc->toJson());

        $response = $this->sendRequest();

        if ( isset($response->error) ) {
            throw $this->createExceptionInstance($response, $uriOptions);
        }
        return $response;
    }

    public function storeAttachment($attachment, $doc,$databaseName)
    {
        throw new \Exception("currently not ready");
    }

    public function getAttachment($attachmentId, $docId,$databaseName)
    {
        throw new \Exception("currently not ready");
    }

    public function getAttachmentContent($attachmentId, $docId,$databaseName)
    {
        throw new \Exception("currently not ready");
    }

    public function getAttachmentAll($doc,$databaseName)
    {
        throw new \Exception("currently not ready");
    }

    public function buildUri($buildOptions)
    {
        $buildOptionsCases = array (
            array('database','docId','parameters'),
            array('database','docId','attachmentId','parameters'),
            array('database','designId','viewId','parameters'),
        );

        $buildOptionsCase = null;
        foreach ( $buildOptionsCases as $key=>$case ) {
            foreach ( $case as $buildOptionKey ) {
                if ( !isset($buildOptions[$buildOptionKey]) ) {
                    continue 2;
                }
            }
            $buildOptionsCase = $key;
            break;
        }

        if ( $buildOptionsCase === null ) {
            $buildOptionKeys = implode(', ',array_keys($buildOptions));
            $msg = sprintf("buildOptions are not supported (%s)",$buildOptionKeys);
            throw new \RuntimeException($msg);
        }

        $database = $buildOptions['database'];

        $databaseData = $this->getConfig()->getDataForIndex($database);
        $uriBuffer = array();

        switch ($buildOptionsCase) {
            case 0:
                $uriBuffer[0][]=$databaseData['host'];
                $uriBuffer[0][]=$database;
                $uriBuffer[0][]=$buildOptions['docId'];
                $uriBuffer[1]=$buildOptions['parameters'];
                break;
            case 1:
                $uriBuffer[0][]=$databaseData['host'];
                $uriBuffer[0][]=$database;
                $uriBuffer[0][]=$buildOptions['docId'];
                $uriBuffer[0][]=$buildOptions['attachmentId'];
                $uriBuffer[1]=$buildOptions['parameters'];
                break;
            case 1:
                $uriBuffer[0][]=$databaseData['host'];
                $uriBuffer[0][]=$database;
                $uriBuffer[0][]='_design';
                $uriBuffer[0][]=$buildOptions['designId'];
                $uriBuffer[0][]='_view';
                $uriBuffer[0][]=$buildOptions['viewId'];
                $uriBuffer[1]=$buildOptions['parameters'];
                break;
        }

        return implode ('/',$uriBuffer[0]).
               '?'.http_build_query($uriBuffer[1]);
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