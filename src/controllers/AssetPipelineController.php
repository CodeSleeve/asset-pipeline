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
			$this->prepareClientCache($absolutePath);
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
		$this->prepareClientCache($path);

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
		$this->prepareClientCache($path);

		$response = Response::make(Asset::stylesheet($path), 200);

		$response->header('Content-Type', 'text/css');

		return $response;
	}

	/**
	 * Prepare client cache so the client doesn't have to download 
	 * the asset again if it has not changed
	 *
	 * @return void
	*/
	private function prepareClientCache($path)
	{
		$lastLastModified = filemtime($path);
		header('Last-Modified: '. gmdate('r', $lastLastModified));
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $lastLastModified)
		{
			header('HTTP/1.0 304 Not Modified');
			exit;
		}
	}
}