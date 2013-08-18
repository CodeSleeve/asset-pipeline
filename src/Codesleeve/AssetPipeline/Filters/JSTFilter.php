<?php
 
namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class JSTFilter implements FilterInterface
{
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
    	$path = $this->getPath($asset);

    	$content = str_replace('"', '\\"', $asset->getContent());
    	$content = str_replace(PHP_EOL, "", $content);

    	$jst = 'JST = (typeof JST === "undefined") ? JST = {} : JST;' . PHP_EOL;
    	$jst .= 'JST["' . $path . '"] = "';
    	$jst .= $content;
    	$jst .= '";' . PHP_EOL;

		$asset->setContent($jst);
    }

    protected function getPath($asset)
    {
    	$base = $asset->getSourceRoot();
    	$file = pathinfo($asset->getSourcePath())['filename'];
        $file = str_replace('\\', '/', $file);
    	$path = '';

    	$searchFor = 'assets' . '/' . 'javascripts';
    	$pos = strpos($base, $searchFor);

    	if ($pos !== false) {
    		$path = ltrim(substr($base, $pos + strlen($searchFor)), '/');
    	}

    	return str_replace('"', '', $path . '/' . $file);
    }
}
