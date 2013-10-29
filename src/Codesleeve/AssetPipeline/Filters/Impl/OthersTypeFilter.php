<?php

namespace Codesleeve\AssetPipeline\Filters\Impl;

use Codesleeve\AssetPipeline\Filters\FileTypeFilter;

class OthersTypeFilter implements FileTypeFilter
{
    /**
     * @inheritdoc
     */
    public function isOfType($file)
    {
        $scriptfilter = new JavascriptsTypeFilter();
        $stylefilter = new StylesheetsTypeFilter();
        return !$scriptfilter->isOfType($file) && !$stylefilter->isOfType($file);
    }
}
