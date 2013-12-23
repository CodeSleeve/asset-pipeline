<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Cache\CacheInterface;

class CacheEnvironmentFilter implements CacheInterface
{
    public $cacheOverride = null;

    public function __construct(CacheInterface $cache, $environment = 'production', $environments = 'production')
    {
        $this->cache = $cache;
        $this->environment = $environment;
        $this->environments = !is_array($environments) ? explode(',', $environments) : $environments;
    }

    public function has($key)
    {
        if ($this->shouldCache()) {
            return $this->cache->has($key);
        }

        return false;
    }

    public function get($key)
    {
        if ($this->shouldCache()) {
            return $this->cache->get($key);
        }
    }

    public function set($key, $value)
    {
        if ($this->shouldCache()) {
            return $this->cache->set($key, $value);
        }
    }

    public function remove($key)
    {
        if ($this->shouldCache()) {
            return $this->cache->remove($key);
        }
    }

    private function shouldCache()
    {
        return !is_null($this->cacheOverride) ? $this->cacheOverride : in_array($this->environment, $this->environments);
    }
}