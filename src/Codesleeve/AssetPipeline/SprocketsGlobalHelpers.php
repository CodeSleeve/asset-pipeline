<?php

if (!function_exists('stylesheet_link_tag'))
{
	function stylesheet_link_tag($file = 'application', $attributes = [])
	{
		return App::make('asset')->stylesheet_link_tag($file, $attributes);
	}
}

if (!function_exists('javascript_include_tag'))
{
	function javascript_include_tag($file = 'application', $attributes = [])
	{
		return App::make('asset')->javascript_include_tag($file, $attributes);
	}
}

if (!function_exists('javascriptIncludeTag'))
{
	function javascriptIncludeTag($file = 'application', $attributes = [])
	{
		return App::make('asset')->javascript_include_tag($file, $attributes);
	}	
}

if (!function_exists('stylesheetLinkTag'))
{
	function stylesheetLinkTag($file = 'application', $attributes = [])
	{
		return App::make('asset')->stylesheet_link_tag($file, $attributes);
	}	
}
