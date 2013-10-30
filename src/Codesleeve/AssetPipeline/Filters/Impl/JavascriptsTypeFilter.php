<?php

namespace Codesleeve\AssetPipeline\Filters\Impl;

use Codesleeve\AssetPipeline\Filters\AbstractFileTypeFilter;

class JavascriptsTypeFilter extends AbstractFileTypeFilter
{
    /**
     * @inheritdoc
     */
    protected $extensions = array(
        '.min.js',
        '.js',
        '.js.coffee',
        '.coffee',
    );
    
    /**
     * @inheritdoc
     */
    protected function overrideableIsOfType($file)
    {
        return $this->defaultIsOfType($file);
    }
}
