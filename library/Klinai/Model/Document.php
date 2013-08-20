<?php

namespace Klinai\Model;


use Klinai\Model\Exception\MarkedAsDeletedException;

use Klinai\Model\Attachment;

use Klinai\Client\AbstractClient;
use Klinai\Model\Exception\InvalidArgumentException;
use Klinai\Model\Exception\AttachmentIsNotExistsException;
use Kline\Model\Database;
use Klinai\Client\ClientAwareTrait;
use Klinai\Model\Exception\DocumentIsMarkedAsDeletedException;

class Document
{
    use ClientAwareTrait;
    use MarkedAsDeletedTrait {
        checkDeleteForDoSomething as checkDeleteForDoSomethingTrait;
    }

    protected $fields;
    protected $sourceDatabase;
    protected $autoRecord;

    public function __construct( $data,AbstractClient $couchClient, $sourceDatabase,$autoRecord=true)
    {
        $this->deleted = false;
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
        $this->checkDeleteForDoSomething();
        $client = $this->getClient();
        $databaseIndex = $this->getSourceDatabase();

        $response = $client->storeDoc($databaseIndex, $this);

        $this->fields->_id= $response->id;
        $this->fields->_rev= $response->rev;
    }

    public function delete()
    {
        $this->checkDeleteForDoSomething();

        $client = $this->getClient();
        $databaseIndex = $this->getSourceDatabase();

        $client->deleteDocument($databaseIndex, $this);
        $this->setDeleted();
    }

    /**
     *
     */
    protected function checkDeleteForDoSomething()
    {
        try {
            $this->checkDeleteForDoSomethingTrait();
        } catch ( MarkedAsDeletedException $preExc) {
            $message = sprintf( 'the document "xy" was deleted in the database. So has every action don`t have a effect', $this->_id );
            throw new DocumentIsMarkedAsDeletedException($message,null,$preExc);
        }
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

    public function updateRev($value)
    {
        if ( !static::validRev($value) ) {
            throw new \RuntimeException(sprintf('the rev "%s" is not valid',$value));
        }

        $this->fields->_rev = $value;
    }

    public static function validRev($value,$oldValue=null)
    {
        if ( !preg_match('/(?<revNumber>\d+)-(?<hash>[a-z0-9]{32})/', $value,$matches) ) {
            return false;
        }
        if ( $oldValue === null ) {
            return true;
        }

        $revNumber = split('-', $oldValue)[0];

        return $matches['revNumber'] > $revNumber;
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
        return $this->get($key);
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
        $this->checkDeleteForDoSomething();
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
        $this->checkDeleteForDoSomething();
        if ( !isset($this->fields->_attachments) ||
             is_array($this->fields->_attachments) && count($this->fields->_attachments)
        ) {
            return array();
        }

        $attachmentsArray = array();
        foreach ( $this->fields->_attachments as $attachmentId=>$attachmentData ) {
            $attachmentsArray[ $attachmentId ] = new Attachment($attachmentId, $attachmentData, $this, $this->getClient());
        }

        return $attachmentsArray;
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