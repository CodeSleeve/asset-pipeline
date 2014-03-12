<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class URLRewrite extends FilterHelper implements FilterInterface
{
    private $baseurl = '';

    public function __construct($baseurl = '', $prefix = '/assets', $paths = array('/app/assets/stylesheets/', '/provider/assets/stylesheets/', '/lib/assets/stylesheets/'))
    {
        $this->baseurl = $baseurl;
        $this->prefix = $prefix;
        $this->paths = $paths;
    }

    public function setAssetPipeline($pipeline)
    {
        $config = $pipeline->getConfig();
        $this->paths = $config['paths'];
    }

    public function filterLoad(AssetInterface $asset)
    {
        // do nothing when this is loaded...
    }

    public function filterDump(AssetInterface $asset)
    {
        $this->root = $asset->getSourceRoot() . '/';
        $this->file = $asset->getSourcePath();
        $this->base = $this->getRelativePath($this->paths, $this->root);

        $content = $this->filterReferences($asset->getContent(), array($this, 'url_matcher'));
        $asset->setContent($content);
    }

    /**
     * My attempt at rewriting CSS urls. I am looking for all url tags and
     * then I am going to try and resolve the correct absolute path from
     * those urls. If the file actually exists then we are good, else I just
     * leave the thing alone.
     *
     * @param  [type] $matches [description]
     * @return [type]          [description]
     */
    public function url_matcher($matches)
    {
        list($changed, $newurl) = $this->relative_match($matches['url']);
        if ($changed) {
            return str_replace($matches['url'], $newurl, $matches[0]);
        }

        list($changed, $newurl) = $this->found_file_match($matches['url']);
        if ($changed) {
            return str_replace($matches['url'], $newurl, $matches[0]);
        }

        return $matches[0];
    }

    /**
     * Search for cases like url(../fonts/blah.eot)
     *
     * @param  string $url
     * @return array(bool, string)
     */
    public function relative_match($url)
    {
        $changed = false;
        $base = $this->base;
        $root = $this->root;

        while (0 === strpos($url, '../') && 2 <= substr_count($base, '/'))
        {
            $changed = true;
            $base = substr($base, 0, strrpos(rtrim($base, '/'), '/') + 1);
            $root = substr($root, 0, strrpos(rtrim($root, '/'), '/') + 1);
            $url = substr($url, 3);
        }

        if (!$changed || !$this->fileExists($root . $url)) {
            return array(false, $url);
        }

        return array(true, $this->baseurl . $this->prefix . $base . $url);
    }

    /**
     * Search for the case where we might be concatenating
     * and so long as the url doesn't start with a '/' we
     * will see if there is a file relative to this directory.
     *
     * @param  string $url
     * @return array(bool, string)
     */
    public function found_file_match($url)
    {
        if ($url[0] != '/' && $this->fileExists($this->root . $url)) {
            return array(true, $this->baseurl . $this->prefix . $this->base . $url);
        }

        return array(false, $url);
    }
}
