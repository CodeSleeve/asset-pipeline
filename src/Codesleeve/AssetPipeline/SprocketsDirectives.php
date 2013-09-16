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
 * Purpose of this class returns a list of valid directives
 */
class SprocketsDirectives extends SprocketsBase {

	private $manifestFile;
	
	const HEADER_PATTERN = '/
		\A (
			(?m:\s*) (
				(\/\* (?s:.*) \*\/) |
				(\#\#\# (?s:.*) \#\#\#) |
				(\/\/ .* \n?)+ |
				(\# .* \n?)+
			)
		)+
	/x';
	
	const DIRECTIVE_PATTERN = '/
		^ \W* = \s* (\w+.*?) ((\*\/)? | (\#\#\#)?) $
	/x';
	
	/**
	 * Extracts the header string of a specific file
	 * 
	 * @param  string $manifestFile file path to extract the header string from
	 * @return string               header string
	 */
	private function getFileHeader($manifestFile)
	{
		$contents = file_get_contents($manifestFile);
		preg_match($this::HEADER_PATTERN, $contents, $matches);
		return isset($matches[0]) ? $matches[0] : '';
	}
	
	/**
	 * Returns an array of directives index by its line number
	 * 
	 * @param  string $manifestFile file path to extract the directives from
	 * @return array                List of directives
	 */
	private function getDirectiveLines($manifestFile)
	{
		$directives = array();
		foreach (preg_split('/\n/', $this->getFileHeader($manifestFile)) as $i => $line) {
			if (preg_match($this::DIRECTIVE_PATTERN, $line, $directive)) {
				$directives[$i + 1] = $directive[1];
			}
		}
		return $directives;
	}
	
	/**
	 * Returns an array of all the files inside of this manifest file
	 * 
	 * @param  string $manifest Filename to open to search for manifest
	 * @return array            List of relative file paths
	 */
	public function getFilesFrom($manifestFile)
	{
		if ($manifestFile === $this->jstFile) {
			return array($jstFile);
		}

		$this->manifestFile = $manifestFile;
		
		$filelist = array();
		foreach ($this->getDirectiveLines($manifestFile) as $line => $directive) {
			$files = $this->processDirective($directive);
			$filelist = array_merge($filelist, $files);
		}
		return array_unique($filelist);
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
