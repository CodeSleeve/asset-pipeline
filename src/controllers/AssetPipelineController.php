<?php namespace Codesleeve\AssetPipeline;

use App, Response, Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetPipelineController extends Controller
{
	/**
	 * Returns a file in the assets directory
	 *
	 * @return \Illuminate\Support\Facades\Response
	 */
	public function file($path)
	{
		$absolutePath = Asset::isJavascript($path);
		if ($absolutePath) {
			return $this->javascript($absolutePath);
		}

		$absolutePath = Asset::isStylesheet($path);
		if ($absolutePath) {
			return $this->stylesheet($absolutePath);
		}

		$absolutePath = Asset::isFile($path);
		if ($absolutePath)
		{
			$this->clientCacheForFile($absolutePath);
			return new BinaryFileResponse($absolutePath, 200);
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
		$response = Response::make(Asset::javascript($path), 200);

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
		$response = Response::make(Asset::stylesheet($path), 200);

		$response->header('Content-Type', 'text/css');

		return $response;
	}

	/**
	 * Client cache regular files that are not
	 * javascript or stylesheets files
	 *
	 * @param  string $path
	 * @return void
	 */
	private function clientCacheForFile($path)
	{
		$lastModified = filemtime($path);

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastModified)
		{
			header('HTTP/1.0 304 Not Modified');
			exit;
		}
	}
}