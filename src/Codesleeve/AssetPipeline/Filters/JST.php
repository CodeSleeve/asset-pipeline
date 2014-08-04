<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class JST extends FilterHelper implements FilterInterface
{
    public function __construct($basePath = '/app/assets/javascripts/')
    {
        $this->basePath = $basePath;
    }

    public function filterLoad(AssetInterface $asset)
    {
        // do nothing when asset is loaded
    }

    public function filterDump(AssetInterface $asset)
    {
        $relativePath = $this->getRelativePath($this->basePath, $asset->getSourceRoot() . '/');
        $filename =  pathinfo($asset->getSourcePath(), PATHINFO_FILENAME);

    	$content = str_replace('"', '\\"', $asset->getContent());
        $content = preg_replace("/[\r?\n]+/", "", $content);

    	$jst = 'JST = (typeof JST === "undefined") ? JST = {} : JST;' . PHP_EOL;
    	$jst .= 'JST["' . $relativePath . $filename . '"] = "';
    	$jst .= $content;
    	$jst .= '";' . PHP_EOL;

		$asset->setContent($jst);
    }
}
