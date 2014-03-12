<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Assetic\Filter\BaseCssFilter;

class FilterHelper extends BaseCssFilter
{
    public function filterLoad(AssetInterface $asset)
    {
        // do nothing when this is loaded...
    }

    public function filterDump(AssetInterface $asset)
    {
        // do nothing when this is dumped...
    }

    public function getRelativePath($basePath, $absolutePath)
    {
        $absolutePath = $this->forwardSlashes($absolutePath);

        if (!is_array($basePath))
        {
            list($changed, $newPath) = $this->_getRelativePath($basePath, $absolutePath);
            return $newPath;
        }

        foreach ($basePath as $path)
        {
            list($changed, $newPath) = $this->_getRelativePath($path, $absolutePath);

            if ($changed) return $newPath;
        }

        return reset($basePath);
    }

    public function fileExists($filename)
    {
        $parsed = parse_url($filename);
        $queryless = isset($parsed['path']) ? $parsed['path'] : $filename;

        return (file_exists($filename) && is_file($filename)) || (file_exists($queryless) && is_file($queryless));
    }

    private function _getRelativePath($basePath, $absolutePath)
    {
        $pos = strpos($absolutePath, $basePath);

        if ($pos !== false)
        {
            $start = strlen($basePath) + $pos;
            $end = strlen($absolutePath) - $start;
            $path = substr($absolutePath, $start, $end);
            return array(true, $path);
        }

        return array(false, $absolutePath);
    }

    /**
     * Swap out any back slashes with forward slashes for
     * windows compatability
     *
     * @param  string $filename
     * @return string
     */
    private function forwardSlashes($filename)
    {
        return str_replace('\\', '/', $filename);
    }
}