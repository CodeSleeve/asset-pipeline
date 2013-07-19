<?php
 
namespace Codesleeve\AssetPipeline\AsseticCustomFilters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class IgnoreFilesFilter implements FilterInterface
{
    private $basePath;
    private $patterns;

    public function __construct($basePath, $patterns = array())
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
            if (strpos($shortFilePath, $this->normalizePath($pattern)) !== false)
            {
                $asset->setContent('');
                return;
            }
        }
    }

    /**
     * [normalizePath description]
     * @param  [type] $path [description]
     * @return [type]       [description]
     */
    private function normalizePath($path)
    {
        return str_replace(array('\\', '/'), DIRECTORY_SEPARATOR, $path);
    }

    /**
     * [str_replace_once description]
     * @param  [type] $str_pattern     [description]
     * @param  [type] $str_replacement [description]
     * @param  [type] $string          [description]
     * @return [type]                  [description]
     */
    private function str_replace_once($str_pattern, $str_replacement, $string)
    {
        $str_pattern = $this->normalizePath($str_pattern);        
        $str_replacement = $this->normalizePath($str_replacement);
        $string = $this->normalizePath($string);

        if (strpos($string, $str_pattern) !== false)
        {
            $occurrence = strpos($string, $str_pattern);
            return substr_replace($string, $str_replacement, strpos($string, $str_pattern), strlen($str_pattern));
        }

        return $string;
    }
}
