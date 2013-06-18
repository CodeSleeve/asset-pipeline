<?php
 
namespace Codesleeve\AssetPipeline\AsseticCustomFilters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class HtmlMinPlusFilter implements FilterInterface
{
    private $basePath;
    private $patterns;

    public function __construct($basePath, $patterns = [])
    {
        $this->basePath = $basePath;
        $this->patterns = $patterns;
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
        $filePath = $asset->getSourceDirectory() . '/' . $asset->getSourcePath();
        $shortFilePath = $this->str_replace_once($this->basePath, "", $filePath);

        foreach($this->patterns as $pattern)
        {
            if (strpos($shortFilePath, $pattern) !== false) {
                return;
            }
        }

		$asset->setContent(preg_replace(array('/ {2,}/', '/<!--.*?-->|\t|(?:\r?\n[ \t]*)+/s'), array(' ',''), $asset->getContent()));
    }

    private function str_replace_once($str_pattern, $str_replacement, $string)
    {
        if (strpos($string, $str_pattern) !== false)
        {
            $occurrence = strpos($string, $str_pattern);
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
        }

        return $string;
    }
}
