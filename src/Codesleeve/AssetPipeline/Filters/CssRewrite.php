<?php
 
namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Filter\BaseCssFilter;

/**
 * My attempt at rewriting urls in css... it seems to work so far...
 * 
 * I manipulated a bit of the code from Assetic\Filter\CssRewrite 
 * (specifically the filterReferences stuff)
 *
 * This will try to resolve the url(...) inside of the css to the 
 * correct place. We know the starting point under asset-pipeline
 * but we still need to manipulate the url some to point it to the
 * right place.
 *
 * When you go from local environment to production environment a
 * common problem is that a relative path say:
 * 
 *     src: url('../font/foobar.eot')
 *     
 * Is not in the right place now according to the webserver because
 * now we are in 
 *     
 *     /assets/application.css
 *     
 * instead of of the original file path 
 * 
 *     /assets/some/path/font/foobar.eot
 *
 * So hopefully this will rewrite the relative url to an absolute path
 * which can be used by both the manifest file AND the original css file
 * 
 *                                             - Kelt <kelt@codesleeve.com>
 */
class CssRewrite extends BaseCssFilter implements FilterInterface 
{
    private $app;
    private $environments;

    /**
     * [initialize description]
     * @param  {[type]} $app [description]
     * @return {[type]}      [description]
     */
    public function initialize($app)
    {
        $this->app = $app;
        $this->asset = $app['asset'];
        $this->prefix = rtrim($app['config']->get('asset-pipeline::routing.prefix'), '/') . '/';
    }

    /**
     * [filterLoad description]
     * @param  AssetInterface $asset [description]
     * @return [type]                [description]
     */
    public function filterLoad(AssetInterface $asset)
    {

    }
 
    /**
     * [filterDump description]
     * @param  AssetInterface $asset [description]
     * @return [type]                [description]
     */
    public function filterDump(AssetInterface $asset)
    {
        if (!$this->setupPaths($asset)) {
            return;
        }

        $content = $this->filterReferences($asset->getContent(), array($this, 'matcher'));

        $asset->setContent($content);
    }

    /**
     * The preg_replace_callback called in seekAndDestoryUrls() eventually makes it's way to 
     * this function to replace all the content in the file where there is a url(...) in the
     * file. We only handle relative paths '../'
     * 
     * @param  {[type]} $matches [description]
     * @return {[type]}          [description]
     */
    public function matcher($matches)
    {
        $changed = false;

        $url = $matches['url'];

        $path = $this->paths['file.base'];

        // relative urls, we are going to try and resolve them
        while (0 === strpos($url, '../') && 2 <= substr_count($path, '/')) {
            $changed = true;
            $path = substr($path, 0, strrpos(rtrim($path, '/'), '/') + 1);
            $url = substr($url, 3);
        }

        // if nothing changed then just keep things as they were
        if (!$changed) {
            return $matches[0];
        }

        // stuff changed so we are going to massage this newurl some...
        $newurl = $path . $url;

        // take off the laravel root path from the url
        $newurl = str_replace($this->paths['app.base'] . '/', '', $newurl);

        // remove any relative paths (in our 'paths' of asset pipeline config)
        foreach ($this->paths['app.paths'] as $relpath)
        {
            if (stripos($newurl, $relpath) === 0) {
                $newurl = ltrim(substr($newurl, strlen($relpath)), '/');
                continue;
            }
        }

        // put the asset pipeline prefix on front of the newurl...        
        $newurl = $this->prefix . $newurl;

        return str_replace($matches['url'], $newurl, $matches[0]);
    }

    /**
     * [setupPaths description]
     * @param  {[type]} $asset [description]
     * @return {[type]}        [description]
     */
    protected function setupPaths($asset)
    {
        $paths = array(
            'file.base'             => $asset->getSourceRoot(),
            'file.path'             => $asset->getSourcePath(),
            'file.target'           => $asset->getTargetPath(),
            'app.base'              => $this->app['path.base'],
            'app.paths'             => $this->asset->paths->get('all')
        );

        if ($paths['file.path'] === null || $paths['file.target'] === null || $paths['file.path'] === $paths['file.target']) {
            return false;
        }

        $this->paths = $paths;

        return true;
    }
}