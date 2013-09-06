<?php

namespace KlinaiTest\Client\TestAsset;

class ObjectToJson {
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function toJson () {
        return json_encode($this->data);
    }
}