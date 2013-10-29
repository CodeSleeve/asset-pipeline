<?php

namespace Codesleeve\AssetPipeline\Filters\Impl;

use Codesleeve\AssetPipeline\Filters\FileTypeFilter;

class StylesheetsTypeFilter implements FileTypeFilter
{
    /**
     * @inheritdoc
     */
    public function isOfType($file)
    {
        $pattern = '/\.(css|less|scss)$/';
        return preg_match($pattern, $file);
    }
}
