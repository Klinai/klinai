<?php

class ObjectToArray {
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function toArray () {
        return $this->data;
    }
}