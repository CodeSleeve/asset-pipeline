<?php

namespace Codesleeve\AssetPipeline;

use Illuminate\Support\Facades\App;
use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class AssetPipelineController extends Controller {

	/*
	 * Returns a javascript file for the given path
	 * 
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function javascript($path1, $path2 = null, $path3 = null, $path4 = null)
	{
		$path1 = ($path2) ? $path1 . "/$path2" : $path1;
		$path1 = ($path3) ? $path1 . "/$path3" : $path1;
		$path1 = ($path4) ? $path1 . "/$path4" : $path1;

		$pipeline = Asset::javascripts($path1);

		$javascript = $pipeline;

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

		$pipeline = Asset::stylesheets($path1);

		$stylesheet = $pipeline;
		
		$response = Response::make($stylesheet, 200);

		$response->header('Content-Type', 'text/css');

		return $response;
	}

}