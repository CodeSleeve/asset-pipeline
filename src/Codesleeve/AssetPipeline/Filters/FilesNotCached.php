<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Cache\CacheInterface;

class FilesNotCached implements CacheInterface
{
    public function has($key)
    {
        return false;
    }

    public function get($key)
    {
        // we don't get anything, because don't cache
    }

    public function set($key, $value)
    {
        // nothing is ever set, because we don't cache
    }

    public function remove($key)
    {
        // nothing is ever removed because we don't cache
    }
}