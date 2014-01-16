<?php namespace Codesleeve\AssetPipeline\Filters;

use DateTime;
use Assetic\Cache\CacheInterface;

class ClientCacheFilter implements CacheInterface
{
    /**
     * This is a decorator class which uses an underlying CacheInterface
     * 
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Proxy the $cache has to see if this asset has been cached yet or not
     * 
     * @param  string  $key
     * @return boolean     
     */
    public function has($key)
    {
        return $this->cache->has($key);
    }

    /**
     * If we make it here then we have a cached version of this asset
     * found in the underlying $cache driver. So we will check the 
     * header HTTP_IF_MODIFIED_SINCE and if that is not less than
     * the last time we cached ($lastModified) then we will exit 
     * with 304 header.
     * 
     * @param  string $key
     * @return string     
     */
    public function get($key)
    {
        $lastModified = $this->getLastTimeModified($key);
        $modifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) : 0;

        header('Last-Modified: '. $lastModified);

        if ($modifiedSince >= strtotime($lastModified))
        {
            header('HTTP/1.0 304 Not Modified');
            exit;
        }

        return $this->cache->get($key);
    }

    /**
     * Proxy set to use the underlying cache driver
     * 
     * @param string $key  
     * @param string $value
     */
    public function set($key, $value)
    {
        return $this->cache->set($key, $value);
    }

    /**
     * Remove this key from session and also remove it from
     * underlying cache driver
     * 
     * @param  string $key
     * @return string     
     */
    public function remove($key)
    {
        if (isset($_SESSION["client.cache.filter.$key"]))
        {
            unset($_SESSION["client.cache.filter.$key"]);
        }

        return $this->cache->remove($key);
    }

    /**
     * Store the last time this file was modified (which we don't know)
     * so we just use the current datetime. We will store this date in
     * $_SESSION so that next time we run we can re-use the datetime we
     * first picked so that our cache isn't busted each time.
     * 
     * @param  string $key
     * @return string     
     */
    private function getLastTimeModified($key)
    {
        if (!isset($_SESSION["client.cache.filter.$key"]))
        {
            $date = new DateTime;
            $_SESSION["client.cache.filter.$key"] = $date->format('r');
        }

        return $_SESSION["client.cache.filter.$key"];
    }
}