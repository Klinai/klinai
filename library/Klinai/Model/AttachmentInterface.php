<?php

namespace Kline\Model;

interface AttachmentInterface
{
    /**
     * @return string
     */
    public function getContent ();

    /**
     * @return string
     */
    public function getId ();
}