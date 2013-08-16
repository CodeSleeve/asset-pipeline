<?php

namespace Codesleeve\AssetPipeline\Test;

class App {
	public function make($dir)
	{
		$app = array();
        $app['env'] = 'local';
        $app["path.base"] = $dir . '/root/sprockets';
        $app['config'] = new Config;

		return $app;
	}
}