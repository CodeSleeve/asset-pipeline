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
	public function javascript($path)
	{
		$pipeline = Asset::javascripts($path);

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
	public function css($path)
	{
		$pipeline = Asset::stylesheets($path);

		$stylesheet = $pipeline;
		
		$response = Response::make($stylesheet, 200);

		$response->header('Content-Type', 'text/css');

		return $response;
	}

}