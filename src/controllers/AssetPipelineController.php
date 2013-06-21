<?php

namespace Codesleeve\AssetPipeline;

use Illuminate\Support\Facades\App;
use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;

class AssetPipelineController extends Controller {

	/*
	 * Returns a javascript file for the given path. We also intelligently
	 * cache the javascript assuming no changes to the directory have been
	 * made.
	 * 
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function javascript($path1, $path2 = null, $path3 = null, $path4 = null)
	{
		$path1 = ($path2) ? $path1 . "/$path2" : $path1;
		$path1 = ($path3) ? $path1 . "/$path3" : $path1;
		$path1 = ($path4) ? $path1 . "/$path4" : $path1;

		$key = 'asset-pipeline-javascripts-' . $path1;
		$this->forgetCache($path1, $key);

		$javascript = (Cache::has($key)) ? Cache::get($key) : Asset::javascripts($path1);

		if (!Cache::has($key)) {
			Cache::forever($key, $javascript);
		}

		$response = Response::make($javascript, 200);

		$response->header('Content-Type', 'application/javascript');

		return $response;
	}

	/**
	 * Returns css for the given path
	 * 
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function css($path1, $path2 = null, $path3 = null, $path4 = null)
	{
		$path1 = ($path2) ? $path1 . "/$path2" : $path1;
		$path1 = ($path3) ? $path1 . "/$path3" : $path1;
		$path1 = ($path4) ? $path1 . "/$path4" : $path1;

		$key = 'asset-pipeline-stylesheets-' . $path1 ;

		$this->forgetCache($path1, $key);

		$stylesheet = (Cache::has($key)) ? Cache::get($key) : Asset::stylesheets($path1);

		if (!Cache::has($key)) {
			Cache::forever($key, $stylesheet);
		}

		$response = Response::make($stylesheet, 200);

		$response->header('Content-Type', 'text/css');

		return $response;
	}

	/**
	 * Returns a file in the assets directory (good for fonts and images)
	 * 
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function file($path1, $path2 = null, $path3 = null, $path4 = null)
	{
		$path1 = ($path2) ? $path1 . "/$path2" : $path1;
		$path1 = ($path3) ? $path1 . "/$path3" : $path1;
		$path1 = ($path4) ? $path1 . "/$path4" : $path1;

		$file = Asset::getPath($path1);

		if (!file_exists($file)) {
			App::abort(404);
		}

		return Response::download($file);
	}

	/**
	 * This function will forget our cached $key when 
	 *
	 * 	- User passes the correct ?forget=<forget password>
	 * 	- We are in development and a file changed in $path
	 * 	- We are in production and we check the $path every <variable> minutes and it's changed
	 *
	 *
	 * We don't want to scan our $path everytime we run our javascript but
	 * we have to in order to know if we have a file that has been changed.
	 * So on production I cache this
	 * @return [type] [description]
	 */
	private function forgetCache($path, $key)
	{
		// user passed a forget parameter
		if (Input::get('forget'))
		{
			if (Input::get('forget') == Config::get('asset-pipeline::forget'))
			{
				Cache::forget($key);
				return;
			}
		}

		// we are in production, so check if we need to scan the $path directory
		$shouldCheck = $key . '-shouldCheck';
		$lastUpdatedAt = $key . '-lastUpdatedAt';

		if (App::environment() == "production" && Cache::has($shouldCheck)) {
			return;
		}

		// we are scanning the directory below so let's mark shouldCheck as good
		if (App::environment() == "production") {
			Cache::put($shouldCheck, 'checked files', Config::get('asset-pipeline::directoryScan'));
		}


		// we are going to scan the $path directory
		$latestTime = Asset::lastUpdatedAt($path);

		// if a file has been changed then lets forget our cache
		if (Cache::get($lastUpdatedAt) != $latestTime)
		{
			Cache::forever($lastUpdatedAt, $latestTime);
			Cache::forget($key);
		}

	}

}