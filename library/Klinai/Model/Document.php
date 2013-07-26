<?php

namespace Klinai\Model;


use Klinai\Model\Attachment;

use Klinai\Client\AbstractClient;
use Klinai\Model\Exception\InvalidArgumentException;
use Klinai\Model\Exception\AttachmentIsNotExistsException;
use Kline\Model\Database;
use Klinai\Client\ClientAwareTrait;

class Document
{
    use ClientAwareTrait;

    protected $fields;
    protected $sourceDatabase;
    protected $autoRecord;

    public function __construct( $data,AbstractClient $couchClient, $sourceDatabase,$autoRecord=true)
    {
        $this->setData($data);
        $this->setClient($couchClient);
        $this->autoRecord = $autoRecord;
        $this->sourceDatabase = $sourceDatabase;
    }

    protected function setData ( $data ) {
        if ( is_array($data) ) {
            $data = (object) $data;
        }

        if (!$data instanceof \stdClass) {
            throw new \RuntimeException("data ist not a instance of \stdClass");
        }
        $this->fields = $data;
    }

    public function setSourceDatabase($sourceDatabase)
    {
        $this->sourceDatabase = $sourceDatabase;
    }

    /**
     *
     * @return Ambigous <Database, unknown>
     */
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

    public function __set($key , $value = NULL)
    {
        $this->set($key,$value);
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
        if ( $this->isAutoRecordEnabled() ) {
            $this->record();
        }
        return TRUE;
    }

    public function __get($key)
    {
        return $this->get();
    }

    public function get($key)
    {
        return $this->has($key) ? $this->fields->$key : NULL;
    }

    public function has($key)
    {
        $key = (string)$key;
        if (!strlen($key) ) throw new InvalidArgumentException("No key given");
        return property_exists( $this->fields,$key ) ? true : false;
    }

    public function getFields()
    {
        return clone $this->fields;
    }

    public function toJson()
    {
        return json_encode($this->fields);
    }

    /**
     *
     * @throws AttachmentIsNotExistsException
     * @return Attachment
     */
    public function getAttachment($attachmentId)
    {
        if ( !$this->isAttachmentExists($attachmentId) ) {
            throw new AttachmentIsNotExistsException(sprintf('the attachment "%s" are not exists',$attachmentId));
        }

        $attachmentData = $this->fields->_attachments->{$attachmentId};

        return new Attachment($attachmentId,$attachmentData,$this,$this->getClient());
    }

    public function isAttachmentExists($attachmentId)
    {
        return isset($this->fields->_attachments->{$attachmentId});
    }

    public function getAttachmentAll()
    {

    }

    public function disableAutoRecord()
    {
        $this->autoRecord = false;
    }

    public function enableAutoRecord()
    {
        $this->autoRecord = true;
    }

    public function setAutoRecord($autoRecord)
    {
        $this->autoRecord = (bool) $autoRecord;
    }

    public function isAutoRecordEnabled()
    {
        return (bool) $this->autoRecord;
    }
}