<?php

/**
 * This allows us to route to the correct assets
 */
Route::group(Config::get('asset-pipeline::routing'), function() {
	Route::get('{path}', Config::get('asset-pipeline::controller_action'))->where('path', '.*');
});