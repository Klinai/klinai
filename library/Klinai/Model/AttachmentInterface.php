<?php

namespace Klinai\Model;

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

    /**
     * @return string
     */
    public function pufferContent ();
}