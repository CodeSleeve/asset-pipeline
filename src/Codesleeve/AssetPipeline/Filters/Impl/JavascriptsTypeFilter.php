<?php

namespace Codesleeve\AssetPipeline\Filters\Impl;

use Codesleeve\AssetPipeline\Filters\FileTypeFilter;

class JavascriptsTypeFilter implements FileTypeFilter
{
    /**
     * @inheritdoc
     */
    public function isOfType($file)
    {
        $pattern = '/\.(js|coffee)$/';
        return preg_match($pattern, $file);
    }
}
