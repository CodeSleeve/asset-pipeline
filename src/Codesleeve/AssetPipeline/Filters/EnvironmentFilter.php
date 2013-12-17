<?php namespace Codesleeve\AssetPipeline\Filters;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;

class EnvironmentFilter implements FilterInterface
{
    private $filter;
    private $environment;
    private $environments;

    public function __construct(FilterInterface $filter, $environment = 'production', $environments = 'production')
    {
        $this->filter = $filter;
        $this->environment = $environment;
        $this->environments = !is_array($environments) ? explode(',', $environments) : $environments;
    }

    public function filterLoad(AssetInterface $asset)
    {
        if ($this->shouldApplyFilter()) {
            $this->filter->filterLoad($asset);
        }
    }

    public function filterDump(AssetInterface $asset)
    {
        if ($this->shouldApplyFilter()) {
            $this->filter->filterDump($asset);
        }
    }

    protected function shouldApplyFilter()
    {
        return in_array($this->environment, $this->environments);
    }
}