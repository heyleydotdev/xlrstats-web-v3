<?php
/**
 * XLRstats : Real Time Player Stats (http://www.xlrstats.com)
 * (CC) BY-NC-SA 2005-2013, Mark Weirath, Özgür Uysal
 *
 * Licensed under the Creative Commons BY-NC-SA 3.0 License
 * Redistributions of files must retain the above copyright notice.
 *
 * Autoloader for the vendored MaxMind DB reader (maxmind-db/reader v1.5.1).
 * Maps the "MaxMind\Db" namespace to the directory this file lives in.
 *
 * @package       app.Vendor.MaxMind
 */

if (!defined('MAXMIND_DB_AUTOLOADER')) {
	define('MAXMIND_DB_AUTOLOADER', true);

	spl_autoload_register(function ($class) {
		$prefix = 'MaxMind\\Db\\';
		if (strpos($class, $prefix) !== 0) {
			return;
		}
		$file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Db' . DIRECTORY_SEPARATOR
			. str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)))
			. '.php';
		if (is_file($file)) {
			require $file;
		}
	});
}
