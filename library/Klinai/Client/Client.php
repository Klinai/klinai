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

    public function getView($databaseName,$designId,$viewId)
    {
        $uriOptions = array(
            'database'=>$databaseName,
            'designId'=>$designId,
            'viewId'=>$viewId,
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
        return $this->resultsToCouchDocuments($response, $databaseName);
    }

    public function storeDocByArray($databaseName, $docData)
    {
        if ( !$docData instanceof \stdClass && !is_array($docData)
        ) {
            throw new RuntimeException("doc is not a instance of (Document or stdClass or Array)");
        }

        $doc = new Document($docData,$this,$databaseName);
        $doc->record();

        return $doc;
    }

    public function storeDoc($databaseName, $doc)
    {
        if ( !$docData instanceof Document &&
             !$docData instanceof \stdClass &&
             !is_array($docData)
        ) {
            throw new RuntimeException("doc is not a instance of (Document or stdClass or Array)");
        }

        if ( !$docData instanceof Document ) {
            $doc = new Document($doc,$this,$databaseName);
        }

        $buildOptions = array(
            'database'=>$databaseName,
            'docId'=>$doc->get('_id'), // if _id not exists, we become NULL
            'parameters'=>$this->getRequestParameters()
        );
        $uri = $this->buildUri($buildOptions);

        $request = $this->getRequest();
        $request->setUri($uri);
        $request->setMethod($doc->has('_id') ? $request::METHOD_PUT : $request::METHOD_POST);
        $request->setContent($doc->toJson());

        $response = $this->sendRequest();

        if ( isset($response->error) ) {
            throw $this->createExceptionInstance($response, $uriOptions);
        }
        return $response;
    }

    public function storeAttachmentByFile($databaseName, Document $doc, $attachmentId, $attachmentFilePath)
    {
        $parameters = array('rev'=>$doc->get('_rev') );

        $uriOptions = array(
            'database'=>$databaseName,
            'docId'=>$doc->get('_id'),
            'attachmentId'=>$attachmentId,
            'parameters'=>array_merge($parameters,$this->getRequestParameters())
        );
        $uri = $this->buildUri($uriOptions);

        $request = $this->getRequest();
        $request->setUri($uri);
        $request->setMethod($request::METHOD_PUT);
        $request->

        $response = $this->sendRequest();

        if ( !is_string($response) && isset($response->error) ) {
            throw $this->createExceptionInstance($response, $uriOptions);
        }
        return $response;
    }

    public function getAttachment($databaseName, $docId, $attachmentId)
    {
        throw new \Exception("currently not ready");
    }

    public function getAttachmentContent($databaseName, $docId, $attachmentId)
    {
        $uriOptions = array(
            'database'=>$databaseName,
            'docId'=>$docId,
            'attachmentId'=>$attachmentId,
            'parameters'=>$this->getRequestParameters()
        );
        $uri = $this->buildUri($uriOptions);

        $request = $this->getRequest();
        $request->setUri($uri);
        $request->setMethod($request::METHOD_GET);

        $response = $this->sendRequest(false);

        if ( !is_string($response) && isset($response->error) ) {
            throw $this->createExceptionInstance($response, $uriOptions);
        }
        return $response;
    }

    public function getAttachmentAll($doc,$databaseName)
    {
        throw new \Exception("currently not ready");
    }

    public function buildUri($buildOptions)
    {
        $buildOptionsCases = array (
            'doc'=>array('database','docId','parameters'),
            'attachment'=>array('database','docId','attachmentId','parameters'),
            'view'=>array('database','designId','viewId','parameters'),
        );

        $buildOptionsCase = null;
        foreach ( $buildOptionsCases as $key=>$case ) {
            if ( count( $case ) !== count($buildOptions) ) {
                continue;
            }

            foreach ( $case as $buildOptionKey ) {
                if ( !isset($buildOptions[$buildOptionKey]) ) {
                    continue 2;
                }
            }
            $buildOptionsCase = $key;
            break;
        }

        if ( $buildOptionsCase === null ) {
            $buildOptionKeys = var_export($buildOptions,true);
            $msg = sprintf("buildOptions are not supported (%s)",$buildOptionKeys);
            throw new \RuntimeException($msg);
        }

        $database = $buildOptions['database'];

        $databaseData = $this->getConfig()->getDataForIndex($database);
        $uriBuffer = array();

        switch ($buildOptionsCase) {
            case 'doc':
                /**
                 * if $buildOptions['docId'] is NULL we become ""
                 * so we have a "host/somedatabase/" URL for a POST request
                 *
                 * this is only important for creating a new Doc
                 */
                $uriBuffer[0][]=$databaseData['host'];
                $uriBuffer[0][]=$database;
                $uriBuffer[0][]=(string) $buildOptions['docId'];

                $uriBuffer[1]=$buildOptions['parameters'];
                break;
            case 'attachment':
                $uriBuffer[0][]=$databaseData['host'];
                $uriBuffer[0][]=$database;
                $uriBuffer[0][]=$buildOptions['docId'];
                $uriBuffer[0][]=$buildOptions['attachmentId'];
                $uriBuffer[1]=$buildOptions['parameters'];
                break;
            case 'view':
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

    public function resultsToCouchDocuments ( $results, $databaseName ) {
        if ( !$results->rows or !is_array($results->rows) )	return FALSE;
        $back = array();
        foreach ( $results->rows as $row ) {	// should have $row->key & $row->doc
            if ( !$row->key or !$row->doc ) 	return false;
            // create couchDocument
            $cd = new Document($row->doc,$this,$databaseName);

            // set key name
            if ( is_string($row->key) ) {
                $key = $row->key;
            }
            elseif ( is_array($row->key) &&
                    !is_array(end($row->key)) &&
                    !is_object(end($row->key))
            ) {
                $key = end($row->key);
            }
            else {
                continue;
            }

            // set value in result array
            if ( !isset($back[$key]) ) {
                $back[$key] = $cd;
            } elseif ( is_array($back[$key]) ) {
                $back[$key][] = $cd;
            } else {
                $back[$key]   = array($back[$key],$cd);
            }
        }
        return $back;
    }
}