<?php

namespace Codesleeve\AssetPipeline\Test;

class Cache {

    public $data = array();
    public function get($key, $default = null)
    {
        return $this->has($key)? $this->data[$key] : $default;
    }

    public function put($key, $value, $minutes = null)
    {
        $this->data[$key] = $value;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    public function forever($key, $value)
    {
        $this->data[$key] = $value;
    }

}