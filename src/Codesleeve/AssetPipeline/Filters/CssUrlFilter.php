<?php
 
namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Util\CssUtils;

class CssUrlFilter implements FilterInterface
{
    /**
     * application instance
     * 
     * @var object
     */
    private $app;
    
    /**
     * current path handler
     * 
     * @var string
     */
    private $path;
    
    /**
     * environments
     * 
     * @var array
     */
    private $environments;
    
    /**
     * asset paths
     * 
     * @var array
     */
    private $paths;
    
    /**
     * routing prefix
     * 
     * @var array
     */
    private $prefix;
    
    /**
	 * [initialize description]
	 * @param  {[type]} $app [description]
	 * @return {[type]}      [description]
	 */
	public function initialize($app)
	{
		$this->app = $app;
        $this->paths = $app['config']->get('asset-pipeline::paths');
        $this->prefix = $app['config']->get('asset-pipeline::routing.prefix', '/assets') . '/';
	}
    
    /**
	 * [__construct description]
	 * @param  string|array $environments list of environments we should minify on
	 * @return null
	 */
	public function __construct($environments = 'production')
	{
		$this->environments = !is_array($environments) ? array($environments) : $environments;
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
        if ($this->shouldProcess()) {
            $content = $asset->getContent();
            $this->path = $this->calculatePath($asset);
            $asset->setContent(
                CssUtils::filterUrls($content, function($matches){
                    $urlparts = explode('/', $matches['url']);
                    $pathparts = explode('/', $this->path);
                    $filepath = array();
                    foreach ($urlparts as $urlpart) {
                        if ($urlpart == '..') {
                            array_pop($pathparts);
                        } else {
                            $filepath[] = $urlpart;
                        }
                    }
                    return str_replace(
                        $matches['url'],
                        implode('/', $pathparts).'/'.implode('/', $filepath),
                        $matches[0]
                    );
                })
            );
            $this->path = '';
        }
    }

    /**
     * calculate the relative path of the asset directory
     * 
     * @param  AssetInterface $asset [description]
     * @return string         returns the relative path of the asset directory
     */
    protected function calculatePath($asset)
    {
        $filepath = $this->normalizePath(
            str_replace($this->app['path.base'] . '/', '', $asset->getSourceRoot())
        );
        foreach($this->paths as $key => $path)
		{
            $compare = is_integer($key) ? $path : $key;
			if (substr($filepath, 0, strlen($compare)) == $compare) {
				$filepath = substr($filepath, strlen($compare));
				continue;
			}
		}
        $filepath = $this->prefix . ltrim($filepath, '/');
        if (isset($this->app['url']))
		{
			return app('url')->asset($filepath, $this->config->get('secure'));
		}
        return $filepath;
    }
    
    /**
	 * This is used to convert any Windows slashes into unix style slashes
	 *
	 * @param [type] $path [description]
	 * @return [type] 	   [description]
	 */
	protected function normalizePath($path)
	{
		return str_replace('\\', '/', $path);
	}
    
    /**
     * [shouldMinify description]
     * @return {[type]} [description]
     */
	protected function shouldProcess()
	{
		return in_array($this->app['env'], $this->environments);
	}
}