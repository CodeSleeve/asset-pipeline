<?php
 
namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class JSTFilter implements FilterInterface
{
    private $app;

    /**
     * [intialize description]
     * @param  [type] $app [description]
     * @return [type]      [description]
     */
    public function initialize($app)
    {
        $this->app = $app;
        $this->paths = $app['asset']->paths->get('javascripts');
    }

	/**
	 * [filterLoad description]
	 * @param  AssetInterface $asset [description]
	 * @return [type]                [description]
	 */
    public function filterLoad(AssetInterface $asset)
    {
        // do nothing when asset is loaded
    }
 
 	/**
 	 * [filterDump description]
 	 * @param  AssetInterface $asset [description]
 	 * @return [type]                [description]
 	 */
    public function filterDump(AssetInterface $asset)
    {
    	$path = $this->getPath($asset);

    	$content = str_replace('"', '\\"', $asset->getContent());
    	$content = str_replace(PHP_EOL, "", $content);

    	$jst = 'JST = (typeof JST === "undefined") ? JST = {} : JST;' . PHP_EOL;
    	$jst .= 'JST["' . $path . '"] = "';
    	$jst .= $content;
    	$jst .= '";' . PHP_EOL;

		$asset->setContent($jst);
    }

    /**
     * Gets the path of the javascript template for me
     * so I know what to put for the key of the JST[] array
     *
     * So if you had 
     * 
     *   /var/www/path/to/laravel_project/app/assets/javascripts/frontend/templates/bar.html
     *   
     * You would end up with JST[frontend/templates/bar] = "<html code here>";
     * 
     * @param  [type] $asset [description]
     * @return [type]        [description]
     */
    protected function getPath($asset)
    {
        $project_base = $this->normalize($this->app['path.base']);

    	$file = pathinfo($asset->getSourcePath());
        $file = $file['filename'];

        $base = $this->normalize($asset->getSourceRoot());
        $base = str_replace($project_base, '', $base);
        $base = $this->strip_beginning_slash($base);

        foreach($this->paths as $path)
        {
            if (strpos($base, $path) === 0)
            {
                $base = $this->strip_beginning_slash(substr($base, strlen($path)));
                continue;
            }
        }
        
        $path = str_replace('"', '', $base . '/' . $file);
        $path = $this->strip_beginning_slash($path);

    	return $path;
    }

    /**
     * Makes all \ slashes go / 
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    protected function normalize($path)
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * Strips off the beginning slash if there is one from the path
     * 
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    protected function strip_beginning_slash($path)
    {
        $prefix = "/";
        if (substr($path, 0, strlen($prefix)) == $prefix) {
            $path = substr($path, strlen($prefix));
        }
        return $path;
    }
}
