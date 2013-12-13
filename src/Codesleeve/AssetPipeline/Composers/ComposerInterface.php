<?php namespace Codesleeve\AssetPipeline\Composers;

interface ComposerInterface
{
    public function process($paths, $absolutePaths, $attributes);
}