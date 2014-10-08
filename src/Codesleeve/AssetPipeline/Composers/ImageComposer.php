<?php namespace Codesleeve\AssetPipeline\Composers;

class ImageComposer extends BaseComposer implements ComposerInterface
{
    /**
     * Process the paths that come through the asset pipeline
     *
     * @param  array $paths
     * @param  array $absolutePaths
     * @param  array $attributes
     * @return void
     */
    public function process($paths, $absolutePaths, $attributes)
    {
        $url = url();
        $attributesAsText = $this->attributesArrayToText($attributes);

        foreach ($paths as $path)
        {
            print "<img src=\"{$url}{$path}\" {$attributesAsText}>" . PHP_EOL;
        }
    }
}