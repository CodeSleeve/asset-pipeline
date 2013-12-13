<?php namespace Codesleeve\AssetPipeline\Composers;

class BaseComposer
{
    /**
     * Convert the attributes array to a html text attributes
     * 
     * @param  array $attributes
     * @return string
     */
    protected function attributesArrayToText($attributes)
    {
        $text = "";

        foreach ($attributes as $name => $value)
        {
            $text .= "{$name} = \"{$value}\" ";
        }

        return $text;
    }
}