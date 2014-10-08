<?php

if (!function_exists('stylesheet_link_tag'))
{
	function stylesheet_link_tag($file = 'application', $attributes = array())
	{
		return App::make('asset')->stylesheetLinkTag($file, $attributes);
	}
}

if (!function_exists('javascript_include_tag'))
{
	function javascript_include_tag($file = 'application', $attributes = array())
	{
		return App::make('asset')->javascriptIncludeTag($file, $attributes);
	}
}

if (!function_exists('javascriptIncludeTag'))
{
	function javascriptIncludeTag($file = 'application', $attributes = array())
	{
		return App::make('asset')->javascriptIncludeTag($file, $attributes);
	}
}

if (!function_exists('stylesheetLinkTag'))
{
	function stylesheetLinkTag($file = 'application', $attributes = array())
	{
		return App::make('asset')->stylesheetLinkTag($file, $attributes);
	}
}

if (!function_exists('imageTag'))
{
	function imageTag($file, $attributes = array())
	{
		return App::make('asset')->imageTag($file, $attributes);
	}
}

if (!function_exists('image_tag'))
{
	function image_tag($file = 'application', $attributes = array())
	{
		return App::make('asset')->imageTag($file, $attributes);
	}
}