<?php

namespace Klinai\Client;

use Klinai\Client\Exception\ConfigIsNotValidException;

class ClientConfig implements ClientConfigInterface
{
    protected $databaseConfig = array();

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
        if ( !isset($this->databaseConfig[$databaseIndex]) ) {
            throw new DatabaseIndexIsNotExistskException(sprintf('Database Index "%s" is not exists',$databaseIndex));
        }

        return $this->databaseConfig[$databaseIndex];
    }

    /**
     * 
     * @param unknown $databaseIndex
     * @throws DatabaseIndexIsNotExistskException
     */
    public function getDataForIndex( $databaseIndex )
    {
        if ( !isset($this->databaseConfig[$databaseIndex]) ) {
            throw new DatabaseIndexIsNotExistskException(sprintf('Database Index "%s" is not exists',$databaseIndex));
        }

        return $this->databaseConfig[$databaseIndex];
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