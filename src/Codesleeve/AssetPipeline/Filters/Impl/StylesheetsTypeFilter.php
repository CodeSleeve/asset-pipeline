<?php

namespace Codesleeve\AssetPipeline\Filters\Impl;

use Codesleeve\AssetPipeline\Filters\AbstractFileTypeFilter;

class StylesheetsTypeFilter extends AbstractFileTypeFilter
{
    /**
     * @inheritdoc
     */
    protected $extensions = array(
        '.min.css',
        '.css',
        '.css.less',
        '.less',
        '.css.scss',
        '.scss',
    );
    
    /**
     * @inheritdoc
     */
    protected function overrideableIsOfType($file)
    {
        return $this->defaultIsOfType($file);
    }
}
