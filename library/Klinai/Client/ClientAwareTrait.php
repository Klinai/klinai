<?php

namespace Klinai\Client;

trait ClientAwareTrait
{
    protected $couchClient;

    /**
     *
     * @return AbstractClient
     */
    protected function getClient()
    {
        return $this->couchClient;
    }

    /**
     *
     * @return AbstractClient
     */
    protected function setClient( AbstractClient $client )
    {
        $this->couchClient = $client;
    }
}