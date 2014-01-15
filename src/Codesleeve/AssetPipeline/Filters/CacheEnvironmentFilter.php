<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Cache\CacheInterface;

class CacheEnvironmentFilter implements CacheInterface
{
    /**
     * Allows us to at anytime turn caching on or off always
     * @var boolean
     */
    public $cacheOverride = null;

    /**
     * Decorator class for CacheInterface $cache that only runs when the shouldCache() function
     * below returns true
     * 
     * @param CacheInterface $cache       
     * @param string         $environment 
     * @param string|array   $environments
     */
    public function __construct(CacheInterface $cache, $environment = 'production', $environments = 'production')
    {
        $this->cache = $cache;
        $this->environment = $environment;
        $this->environments = !is_array($environments) ? explode(',', $environments) : $environments;
    }

    /**
     * If we should cache then we proxy thru to $this->cache
     * else we always return false which causes assets to be rebuilt
     * 
     * @param  string  $key
     * @return boolean      
     */
    public function has($key)
    {
        if ($this->shouldCache()) {
            return $this->cache->has($key);
        }

        return false;
    }

    /**
     * Proxy thru to $cache
     * 
     * @param  string $key
     * @return string     
     */
    public function get($key)
    {
        return $this->cache->get($key);
    }

    /**
     * Proxy thru to $cache
     * 
     * @param string $key  
     * @param string $value
     */
    public function set($key, $value)
    {
        return $this->cache->set($key, $value);
    }

    /**
     * Proxy thru to $cache
     * 
     * @param  string $key
     * @return string
     */
    public function remove($key)
    {
        return $this->cache->remove($key);
    }

    /**
     * If cache override is not set then we just check to see if our
     * current $environment is in the list of $environments. By default
     * this is just 'production' and array('production'). So this only
     * runs cache if we are on production environment
     * 
     * @return boolean
     */
    private function shouldCache()
    {
        return !is_null($this->cacheOverride) ? $this->cacheOverride : in_array($this->environment, $this->environments);
    }
}