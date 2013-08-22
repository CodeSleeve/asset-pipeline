<?php

namespace Codesleeve\AssetPipeline\Test;

class App {
	public static function make($dir)
	{
		$app = array();
        $app['env'] = 'local';
        $app["path.base"] = $dir . '/root/sprockets';
        $app['config'] = new Config;
        $app['cache'] = new Cache;
        $app['events'] = new Events;
        
		return $app;
	}
}