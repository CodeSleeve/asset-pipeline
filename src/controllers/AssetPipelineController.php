<?php

namespace Codesleeve\AssetPipeline;

use Illuminate\Support\Facades\App;
use Illuminate\Routing\Controllers\Controller;
use Illuminate\Support\Facades\Response;

class AssetPipelineController extends Controller {

	/**
	 * Returns a file in the assets directory
	 * 
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function file($path)
	{
		$file = Asset::getFullPath($path);

		if (Asset::isJavascript($path)) {
			return $this->javascript($path);			
		}
		
		if (Asset::isStylesheet($path)) {
			return $this->stylesheet($path);
		} 

		if (file_exists($file)) {
			return Response::download($file);
		}

		App::abort(404);
	}

	/*
	 * Returns a javascript file for the given path.
	 * 
	 * @return \Illuminate\Support\Facades\Response
	 */
	private function javascript($path)
	{
		$javascript = AssetCache::javascripts($path);

		$response = Response::make($javascript, 200);

		$response->header('Content-Type', 'application/javascript');

		return $response;
	}

	/**
	 * Returns css for the given path
	 * 
	 * @return \Illuminate\Support\Facades\Response
	 */
	private function stylesheet($path)
	{
		$stylesheet = AssetCache::stylesheets($path);

		$response = Response::make($stylesheet, 200);

		$response->header('Content-Type', 'text/css');

		return $response;
	}

}