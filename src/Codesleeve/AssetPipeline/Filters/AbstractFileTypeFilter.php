<?php

namespace Codesleeve\AssetPipeline\Filters;

abstract class AbstractFileTypeFilter implements FileTypeFilter
{
    /**
     * filter type provider
     * 
     * @var FilterTypeProvider 
     */
    protected $provider;
    
	/**
	 * default filtered file extensions
	 * 
	 * @var array 
	 */
	protected $extensions = array();
	
	/**
	 * constructor
	 * 
	 * @param array $extensions additional extensions to filter
	 */
	public function __construct(FilterTypeProvider $provider, $extensions = array())
	{
        $this->provider = $provider;
		$this->extensions = array_unique(
			array_merge($this->extensions, $extensions)
		);
	}
	
	/**
	 * @inheritdoc
	 */
	final public function isOfType($file) {
		return $this->overrideableIsOfType($file);
	}
	
	/**
	 * implementation of AbstractFileTypeFilter::isOfType
	 */
	abstract protected function overrideableIsOfType($file);
	
	/**
	 * default implementation of AbstractFileTypeFilter::isOfType that can be used
	 * by AbstractFileTypeFilter subclasses
	 */
	final protected function defaultIsOfType($file)
	{
		return in_array('.'.pathinfo($file, PATHINFO_EXTENSION), $this->extensions);
	}
}
