<?php

namespace Codesleeve\AssetPipeline;

class AssetETag
{
    /**
     * [__construct description]
     * @param [type] $app
     */
    public function __construct($app)
    {
        $this->env = $app['env'];
        $this->cache = $app['cache'];
        $this->config = $app['config'];
        $this->asset = $app['asset'];
        $this->request = $app['request'];
    }

    /**
     * This is used to scan cached etags and return 304s if satisfied
     * 
     * Thanks to http://fideloper.com/laravel4-etag-conditional-get for an
     * easy to understand way of doing this
     * 
     * @return [type] [description]
     */
    public function hasValidEtag($path)
    {
        $etag = $this->request->getETags();

        if (isset($etag[0]))
        {
            // Symfony getEtags() method returns ETags in double quotes :/
            $etag = str_replace('"', '', $etag[0]);

            if ($etag === $this->getEtag($path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the javascript etag for this path
     * 
     * @param  string $path
     * @return md5 partial string
     */
    public function getEtag($path)
    {
        $filepath = $this->asset->getFullPath($path);
        $filetime = filemtime($filepath);

        return md5($filepath + $filetime);
    }

}