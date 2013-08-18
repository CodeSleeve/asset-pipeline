<?php

namespace Codesleeve\AssetPipeline\Test;

class App {
	public function make($dir)
	{
		$app = array();
        $app['env'] = 'local';
        $app["path.base"] = $dir . '/root/sprockets';
        $app['config'] = new Config;
        $app['cache'] = new Cache;
        $app['event'] = new Event;
        
		return $app;
	}
}