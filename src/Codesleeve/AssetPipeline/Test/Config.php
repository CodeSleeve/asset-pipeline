<?php

namespace Codesleeve\AssetPipeline\Test;

class Config {

    public $data = array();
    
    public function __construct()
    {
        $this->data = include __DIR__ . '/../../../config/config.php';
    }

    public function get($pathname, $value = null)
    {
        $pathname = str_replace('asset-pipeline::', '', $pathname);
        return array_key_exists($pathname, $this->data) ? $this->data[$pathname] : $value;
    }

    public function set($pathname, $value)
    {
        $this->data[$pathname] = $value;        
    }
}