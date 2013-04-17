<?php

namespace Klinai\Client;

use Klinai\Client\Exception\DatabaseIndexIsNotExistsException;
use Klinai\Client\Exception\ConfigIsNotValidException;
use RuntimeException;

class ClientConfig implements ClientConfigInterface
{
    protected $databaseConfig = array();
    protected $database = array();

    public function __construct($config = null)
    {
        if ( $config != null ) {
            $this->setConfig($config);
        }

    }

    /**
     * 
     * @param unknown $databaseIndex
     * @throws DatabaseIndexIsNotExistskException
     */
    public function getDatabaseIndex( $databaseIndex )
    {
        if ( !$this->hasDatabase($databaseIndex) && !isset($this->database[$databaseIndex]) ) {
            throw new DatabaseIndexIsNotExistsException(sprintf('Database Index "%s" is not exists',$databaseIndex));
        }
        if ( !isset($this->database[$databaseIndex]) ) {
            try {
                throw new RuntimeException("currently not ready");
//                 $this->database[$databaseIndex] = new 
            } catch (\Exception $e) {
                throw new RuntimeException(sprintf('Database Object "%s" can\'t be created',$databaseIndex));
            }
        }

        return $this->database[$databaseIndex];
    }

    /**
     * 
     * @param unknown $databaseIndex
     * @throws DatabaseIndexIsNotExistskException
     */
    public function getDataForIndex( $databaseIndex )
    {
        if ( !$this->hasDatabase($databaseIndex) ) {
            throw new DatabaseIndexIsNotExistsException(sprintf('Database Index "%s" is not exists',$databaseIndex));
        }

        return $this->databaseConfig[$databaseIndex];
    }

    public function getAllDatabase()
    {
        return array_keys($this->databaseConfig);
    }

    public function hasDatabase($databaseIndex)
    {
        return isset($this->databaseConfig[$databaseIndex]);
    }

    /**
     * 
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->validateConfig($config);

        $this->databaseConfig = $config['databases'];
    }

    /**
     * 
     * @param array $config
     * @param boolean $throw
     * @throws ConfigIsNotValidException
     * @return boolean
     */
    public function isConfigValid($config,$throw=false)
    {
        try {
            $this->validateConfig($config);

            return true;
        } catch ( ConfigIsNotValidException $exception ) {
            if ( $throw ) {
                throw new ConfigIsNotValidException ("config is not valid",null,$exception);
            }
        }
        return false;
    }

    /**
     * 
     * @param array $config
     * @throws Exception\ConfigIsNotValidException
     * @return void
     */
    public function validateConfig(array $config)
    {
    }
}