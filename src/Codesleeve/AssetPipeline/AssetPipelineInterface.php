<?php

namespace Codesleeve\AssetPipeline;

interface AssetPipelineInterface
{
	public function javascripts($path = 'javascripts');
	public function stylesheets($path = 'stylesheets');
}