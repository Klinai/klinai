<?php

namespace Klinai\Model;


use Klinai\Client\AbstractClient;
use Klinai\Model\Exception\InvalidArgumentException;

class Document
{
    protected $fields;
    protected $couchClient;
    protected $sourceDatabase;
    protected $autoRecord;

    public function __construct(\stdClass $data,AbstractClient $couchClient,$sourceDatabase,$autoRecord=true)
    {
        $this->fields=$data;
        $this->couchClient=$couchClient;
        $this->autoRecord = $autoRecord;
        $this->sourceDatabase = $sourceDatabase;
    }

    public function setSourceDatabase($sourceDatabase)
    {
        $this->sourceDatabase = $sourceDatabase;
    }

    public function getSourceDatabase()
    {
        return $this->sourceDatabase;
    }

    public function record()
    {
        $client = $this->getClient();
        $databaseIndex = $this->getSourceDatabase();
        
        
        $response = $client->storeDoc($databaseIndex, $this);

        $this->fields->_id= $response->id;
        $this->fields->_rev= $response->rev;
    }

    protected function setOne($key, $value)
    {
        $key = (string)$key;
        if ( !strlen($key) )  throw new InvalidArgumentException("property name can't be empty");
        if ( $key == '_rev' ) throw new InvalidArgumentException("Can't set _rev field");
        if ( $key == '_id' AND $this->get('_id') ) throw new InvalidArgumentException("Can't set _id field because it's already set");
        if ( substr($key,0,1) == '_' AND !in_array($key,couchClient::$allowed_underscored_properties) )
            throw new InvalidArgumentException("Property $key can't begin with an underscore");
        
        $this->fields->$key = $value;
        return TRUE;
    }

    /**
     * 
     * @return AbstractClient
     */
    protected function getClient()
    {
        return $this->couchClient;
    }

    public function set($key , $value = NULL)
    {
        if ( func_num_args() == 1 ) {
            if ( !is_array($key) AND !is_object($key) ) throw new InvalidArgumentException("When second argument is null, first argument should ba an array or an object");
            foreach ( $key as $oneKey => $oneValue ) {
                $this->setOne($oneKey,$oneValue);
            }
        } else {
            $this->setOne($key,$value);
        }
        if ( $this->autoRecord ) {
            $this->record();
        }
        return TRUE;
    }

    public function get($key)
    {
        $key = (string)$key;
        if (!strlen($key) ) throw new InvalidArgumentException("No key given");
        return property_exists( $this->fields,$key ) ? $this->fields->$key : NULL;
    }

    public function getFields()
    {
        return clone $this->fields;
    }

    public function toJson()
    {
        return json_encode($this->fields);
    }
}