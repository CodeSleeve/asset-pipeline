<?php

namespace Codesleeve\AssetPipeline\Test;

class Request {

    public $data = array();

    public function getEtags()
    {
        if (!array_key_exists('etag', $this->data)) {
            $this->setEtag('');
        }

        return $this->data['etag'];
    }

    public function setEtag($value)
    {
        $this->data['etag'] = array($value);
    }
}