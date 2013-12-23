<?php


class App
{
	static public function environment()
	{
		return 'local';
	}

	static public function make($make)
	{
		if ($make == 'path.storage') {
			return __DIR__ . "/storage";
		}

		return null;
	}
}