<?php namespace Codesleeve\AssetPipeline\Composers;

class JavascriptComposer extends BaseComposer implements ComposerInterface
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
        $text = $this->attributesArrayToText($attributes);

        foreach ($paths as $path)
        {
            print "<script src=\"{$path}\" {$text}></script>" . PHP_EOL;
        }
    }
}