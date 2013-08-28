<?php

namespace Codesleeve\AssetPipeline;

use Codesleeve\AssetPipeline\Directives\BaseDirective;
use Codesleeve\AssetPipeline\Directives\RequireDirectory;
use Codesleeve\AssetPipeline\Directives\RequireTree;
use Codesleeve\AssetPipeline\Directives\RequireSelf;
use Codesleeve\AssetPipeline\Directives\RequireFile;
use Codesleeve\AssetPipeline\Directives\DependOn;
use Codesleeve\AssetPipeline\Directives\IncludeJST;
use Codesleeve\AssetPipeline\Directives\IncludeFile;
use Codesleeve\AssetPipeline\Directives\Stub;


/**
 * Purpose of this class returns a list of 
 */
class SprocketsDirectives extends SprocketsBase {

	private $manifestFile;

	/**
	 * Returns an array of all the files inside of this manifest file
	 * 
	 * @param  string $manifest Filename to open to search for manifest
	 * @return array           	List of relative file paths
	 */
	public function getFilesFrom($manifestFile)
	{
		if ($manifestFile === $this->jstFile) {
			return array($jstFile);
		}

		$this->manifestFile = $manifestFile;

		$filelist = array();
		$lines = ($manifestFile) ? file($manifestFile) : array();

		foreach ($lines as $line)
		{
			$files = $this->findFilesFromDirective($line);
			if ($files) {
				$filelist = array_merge($filelist, $files);
			}
		}

		return array_unique($filelist);
	}

	/**
	 * Returns an array of files from the directive on this $line
	 * 
	 * @param  string            $line      This is the potential directive line
	 * @param  array             $tokens    This is a list of valid tokens
	 */
	private function findFilesFromDirective($line, $tokens = array('//=', '*=', '#='))
	{
		$line = ltrim($line);
		
		if (!$line) {
			return false;
		}

		foreach ($tokens as $token) {
			if (strpos($line, $token) === 0) {
				$directive = trim(substr($line, strlen($token)));
				return $this->processDirective($directive);
			}
		}

		return false;
	}

	/**
	 * Returns an array of file(s) based on directive
	 * @param  {[type]} $directive
	 * @return {[type]}
	 */
	private function processDirective($line)
	{
		$directives = array(
			'require ' => new RequireFile($this->app, $this->manifestFile),
			'require_directory' => new RequireDirectory($this->app, $this->manifestFile),
			'require_tree' => new RequireTree($this->app, $this->manifestFile),
			'require_self' => new RequireSelf($this->app, $this->manifestFile),
			'include_jst' => new IncludeJST($this->app, $this->manifestFile),
			'depend_on ' => new DependOn($this->app, $this->manifestFile),
			'include ' => new IncludeFile($this->app, $this->manifestFile),
			'stub ' => new Stub($this->app, $this->manifestFile)
		);

		foreach ($directives as $directive_name => $directive) {
			$param = $this->checkForDirective($directive_name, $line);
			
			if (!is_null($param)) {
				return $directive->process($param);
			}
		}

		$directive = new BaseDirective($this->app, $this->manifestFile);
		return $directive->process_basic($line);
	}

	/**
	 * See if the directive and diretive name match and if so, then we have a match and
	 * should return the parameters of this directive on this line
	 * 
	 * @param  {[type]} $directive_name
	 * @param  {[type]} $directive
	 * @return {[type]}
	 */
	private function checkForDirective($directive_name, $directive)
	{
		if (strpos($directive, $directive_name) === 0) {
		 	$param = trim(substr($directive, strlen($directive_name)));
		 	return ($param) ? $param : true;
		}

		return null;
	}

}
