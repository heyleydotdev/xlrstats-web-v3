<?php
/**
 * XLRstats : Real Time Player Stats (http://www.xlrstats.com)
 * (CC) BY-NC-SA 2005-2013, Mark Weirath, Özgür Uysal
 *
 * Licensed under the Creative Commons BY-NC-SA 3.0 License
 * Redistributions of files must retain the above copyright notice.
 *
 * @link          http://www.xlrstats.com
 * @license       Creative Commons BY-NC-SA 3.0 License (http://creativecommons.org/licenses/by-nc-sa/3.0/)
 * @package       app.Plugin.Dashboard.Controller
 * @since         XLRstats v3.0
 * @version       0.1
 */

App::uses('DashboardAppController', 'Dashboard.Controller');
App::uses('HttpSocket', 'Network/Http');
App::import('Vendor', 'MaxMindDbAutoloader', array('file' => 'MaxMind' . DS . 'bootstrap.php'));

/**
 * Class MaintenanceController
 */
class MaintenanceController extends DashboardAppController {

/**
 * @var array
 */
	public $uses = array();

/**
 * Definitions of the downloadable geo IP databases
 *
 * @var array
 */
	protected $_geoDatabases = array(
		'country' => array(
			'file' => 'dbip-country-lite.mmdb',
			'label' => 'Country',
			'url' => 'https://download.db-ip.com/free/dbip-country-lite-%s.mmdb.gz',
		),
		'city' => array(
			'file' => 'dbip-city-lite.mmdb',
			'label' => 'City',
			'url' => 'https://download.db-ip.com/free/dbip-city-lite-%s.mmdb.gz',
		),
	);

//-------------------------------------------------------------------

/**
 * Geo IP database management is a super admin task. These actions have no ACO records, so we
 * additionally stack ControllerAuthorize (which grants group_id 1) on top of the regular
 * ActionsAuthorize checks instead of forcing an ACL update for them.
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if (isset($this->user['AppUser']['group_id']) && $this->user['AppUser']['group_id'] == 1) {
			$this->Auth->authorize = array(
				'Actions' => array(
					'actionPath' => 'controllers/',
					'userModel' => 'AppUser',
				),
				'Controller',
			);
		}
	}

//-------------------------------------------------------------------

/**
 * admin_index method
 */
	public function admin_index() {
	}

//-------------------------------------------------------------------

/**
 * Clears the cache
 */
	public function admin_clearCache() {
		//Nothing here, the view file will handle the clearing of the cache.
	}

//-------------------------------------------------------------------

/**
 * Geo IP databases status page
 */
	public function admin_geoDb() {
		$this->set('title_for_layout', __('Geo IP Databases • XLRstats'));
		$this->set('dbPath', $this->_geoDbDir());
		$status = array();
		foreach ($this->_geoDatabases as $type => $config) {
			$config['type'] = $type;
			$status[$type] = $this->_geoDbStatus($config);
		}
		$this->set(compact('status'));
	}

//-------------------------------------------------------------------

/**
 * Downloads and installs a geo IP database ('country' or 'city').
 * Tries the current month's DB-IP Lite release first and falls back
 * to last month's while the new one has not been published yet.
 *
 * Returns JSON for ajax requests (used by the progress UI), a regular
 * flash message redirect otherwise.
 *
 * @param null $type
 */
	public function admin_geoDbUpdate($type = null) {
		$isAjax = $this->request->is('ajax');
		if ($isAjax) {
			session_write_close(); // unblock concurrent progress polling requests
		}

		if (!isset($this->_geoDatabases[$type])) {
			return $this->_geoDbResult(false, __('Unknown database type.'), $isAjax);
		}

		$config = $this->_geoDatabases[$type];
		set_time_limit(0);

		$dir = $this->_geoDbDir();
		if (!is_dir($dir)) {
			mkdir($dir, 0775, true);
		}
		if (!is_writable($dir)) {
			CakeLog::write('geoip', 'Geo IP database directory "' . $dir . '" is not writable.');
			$this->_clearProgress($type);
			return $this->_geoDbResult(false, __('The database directory is not writable by the web server.'), $isAjax);
		}

		// Download: try this month's release first, then last month's.
		$gzPath = $dir . DS . 'download.tmp.gz';
		$error = '';
		foreach (array(date('Y-m'), date('Y-m', strtotime('-1 month'))) as $month) {
			$this->_writeProgress($type, array(
				'phase' => 'downloading',
				'downloaded' => 0,
				'total' => 0,
				'month' => $month,
			));
			$url = sprintf($config['url'], $month);
			if ($this->_downloadFile($url, $gzPath, $type)) {
				$error = '';
				break;
			}
			$error = 'Download failed: ' . $url;
			@unlink($gzPath);
		}
		if (!empty($error)) {
			CakeLog::write('geoip', $error);
			$this->_clearProgress($type);
			return $this->_geoDbResult(false, __('Could not download the %s database. Check the error log for details.', $config['label']), $isAjax);
		}

		if (!$this->_installGzipDatabase($gzPath, $dir . DS . $config['file'], $type)) {
			$this->_clearProgress($type);
			return $this->_geoDbResult(false, __('Downloaded %s database file appears to be invalid.', $config['label']), $isAjax);
		}

		$this->_clearProgress($type);
		CakeLog::write('geoip', ucfirst($config['label']) . ' database updated successfully.');
		return $this->_geoDbResult(true, __('%s database installed successfully.', $config['label']), $isAjax);
	}

//-------------------------------------------------------------------

/**
 * Reports live download/installation progress for all databases.
 *
 * @return void
 */
	public function admin_geoDbStatus() {
		$this->viewClass = 'Json';
		session_write_close(); // never block the polling requests

		$progress = array();
		foreach ($this->_geoDatabases as $type => $config) {
			$progress[$type] = $this->_readProgress($type);
		}
		$this->set(compact('progress'));
		$this->set('_serialize', 'progress');
	}

//-------------------------------------------------------------------

/**
 * Renders a success/error response either as JSON (ajax) or as a
 * flash message redirect (regular navigation).
 *
 * @param bool $success
 * @param string $message
 * @param bool $isAjax
 * @return mixed
 */
	protected function _geoDbResult($success, $message, $isAjax) {
		if (!$isAjax) {
			$class = $success ? 'default' : 'error';
			$this->Session->setFlash($message, 'default', array('class' => 'message ' . $class));
			return $this->redirect(array('action' => 'admin_geoDb'));
		}
		$this->viewClass = 'Json';
		$this->set(array(
			'success' => $success,
			'message' => $message,
			'_serialize' => array('success', 'message'),
		));
	}

//-------------------------------------------------------------------

/**
 * Streams a URL to a local file using cURL when available,
 * falling back to HttpSocket otherwise. Writes live progress
 * information for the status endpoint.
 *
 * @param string $url
 * @param string $target
 * @param string $type database identifier used for progress reporting
 * @return bool
 */
	protected function _downloadFile($url, $target, $type) {
		if (function_exists('curl_init')) {
			$handle = @fopen($target, 'wb');
			if ($handle === false) {
				return false;
			}
			$progressFile = $this->_progressFile($type);
			$lastWrite = 0;

			$curl = curl_init($url);
			curl_setopt_array($curl, array(
				CURLOPT_FILE => $handle,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS => 5,
				CURLOPT_CONNECTTIMEOUT => 15,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FAILONERROR => true,
				CURLOPT_LOW_SPEED_LIMIT => 1024,
				CURLOPT_LOW_SPEED_TIME => 60,
				CURLOPT_USERAGENT => 'XLRstats webfront v3',
				CURLOPT_NOPROGRESS => false,
				CURLOPT_BUFFERSIZE => 131072,
				CURLOPT_PROGRESSFUNCTION => function () use ($progressFile, &$lastWrite) {
					// Argument layout differs between PHP versions; the last
					// two arguments are always (expected total, downloaded so far).
					$args = func_get_args();
					$total = (int)$args[count($args) - 2];
					$downloaded = (int)$args[count($args) - 1];
					if (time() - $lastWrite >= 1) {
						$lastWrite = time();
						@file_put_contents($progressFile, json_encode(array(
							'phase' => 'downloading',
							'downloaded' => $downloaded,
							'total' => $total,
						)));
					}
					return 0; // continue the transfer
				},
			));
			$result = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			fclose($handle);
			if ($result === false || $httpCode != 200) {
				@unlink($target);
				return false;
			}
			return true;
		}

		try {
			$HttpSocket = new HttpSocket(array('timeout' => 600));
			$response = $HttpSocket->get($url, null, array('redirect' => true));
		} catch (Exception $e) {
			CakeLog::write('geoip', 'Download failed: ' . $e->getMessage());
			return false;
		}
		if ($response->code != 200 || empty($response->body)) {
			return false;
		}
		return file_put_contents($target, $response->body) !== false;
	}

//-------------------------------------------------------------------

/**
 * Verifies a downloaded .mmdb.gz file, gunzips it next to its target
 * path and atomically moves it into place.
 *
 * @param string $gzPath
 * @param string $target
 * @param string $type database identifier used for progress reporting
 * @return bool
 */
	protected function _installGzipDatabase($gzPath, $target, $type) {
		clearstatcache();
		if (!is_file($gzPath) || filesize($gzPath) < 1024) {
			return false;
		}
		$magic = @file_get_contents($gzPath, false, null, 0, 2);
		if ($magic !== "\x1F\x8B") {
			return false;
		}

		$tmpTarget = $target . '.tmp';
		$source = @gzopen($gzPath, 'rb');
		$dest = @fopen($tmpTarget, 'wb');
		if ($source === false || $dest === false) {
			if (is_resource($source)) {
				gzclose($source);
			}
			if (is_resource($dest)) {
				fclose($dest);
			}
			@unlink($tmpTarget);
			@unlink($gzPath);
			return false;
		}

		$this->_writeProgress($type, array(
			'phase' => 'extracting',
			'downloaded' => filesize($gzPath),
			'total' => filesize($gzPath),
		));

		$lastWrite = time();
		$uncompressed = 0;
		while (!gzeof($source)) {
			$chunk = gzread($source, 262144);
			fwrite($dest, $chunk);
			$uncompressed += strlen($chunk);
			if (time() - $lastWrite >= 2) {
				$lastWrite = time();
				$this->_writeProgress($type, array(
					'phase' => 'extracting',
					'downloaded' => $uncompressed,
					'total' => null,
				));
			}
		}
		gzclose($source);
		fclose($dest);

		// Sanity checks: plausible size and readable MMDB metadata marker.
		clearstatcache();
		if (filesize($tmpTarget) < 524288 || strpos(@file_get_contents($tmpTarget, false, null, max(0, filesize($tmpTarget) - 4096)), 'MaxMind.com') === false) {
			@unlink($tmpTarget);
			@unlink($gzPath);
			return false;
		}

		$installed = rename($tmpTarget, $target);
		@unlink($gzPath);
		return $installed;
	}

//-------------------------------------------------------------------

/**
 * Directory holding the geo IP databases. Defaults to the writable
 * app/tmp/geoip folder and can be overridden with Configure::read('GeoIP.dbPath')
 *
 * @return string
 */
	protected function _geoDbDir() {
		$dir = Configure::read('GeoIP.dbPath');
		if (empty($dir)) {
			$dir = APP . 'tmp' . DS . 'geoip';
		}
		return $dir;
	}

//-------------------------------------------------------------------

/**
 * Collects status information for a single database definition
 *
 * @param array $config
 * @return array
 */
	protected function _geoDbStatus($config) {
		$path = $this->_geoDbDir() . DS . $config['file'];
		$info = array(
			'type' => $config['type'],
			'label' => $config['label'],
			'path' => $path,
			'available' => false,
		);
		if (!is_readable($path)) {
			$info['error'] = __('Not downloaded yet');
			return $info;
		}
		clearstatcache();
		$info['available'] = true;
		$info['size'] = filesize($path);
		$info['modified'] = filemtime($path);
		$info['sizeReadable'] = CakeNumber::toReadableSize($info['size']);
		try {
			$reader = new \MaxMind\Db\Reader($path);
			$metadata = $reader->metadata();
			$info['databaseType'] = $metadata->databaseType;
			$info['buildDate'] = date('Y-m-d', $metadata->buildEpoch);
			$reader->close();
		} catch (Exception $e) {
			$info['available'] = false;
			$info['error'] = __('File is not a valid database');
			CakeLog::write('geoip', 'Invalid database at "' . $path . '": ' . $e->getMessage());
		}
		return $info;
	}

//-------------------------------------------------------------------

/**
 * Path of the live progress file of a database download
 *
 * @param string $type
 * @return string
 */
	protected function _progressFile($type) {
		$dir = APP . 'tmp' . DS . 'geoip';
		if (!is_dir($dir)) {
			mkdir($dir, 0775, true);
		}
		return $dir . DS . $type . '.json';
	}

//-------------------------------------------------------------------

/**
 * Updates the live progress file of a download
 *
 * @param string $type
 * @param array $data
 */
	protected function _writeProgress($type, $data) {
		@file_put_contents($this->_progressFile($type), json_encode($data));
	}

//-------------------------------------------------------------------

/**
 * Reads the live progress file of a download, ignoring entries that
 * look stale (e.g. after an aborted request)
 *
 * @param string $type
 * @return array|null
 */
	protected function _readProgress($type) {
		$file = APP . 'tmp' . DS . 'geoip' . DS . $type . '.json';
		if (!is_file($file)) {
			return null;
		}
		$data = json_decode(@file_get_contents($file), true);
		if (!is_array($data)) {
			@unlink($file);
			return null;
		}
		// An active download touches the file every second or two.
		clearstatcache();
		if (time() - filemtime($file) > 120) {
			@unlink($file);
			return null;
		}
		return $data;
	}

//-------------------------------------------------------------------

/**
 * Removes the live progress file of a download
 *
 * @param string $type
 */
	protected function _clearProgress($type) {
		@unlink(APP . 'tmp' . DS . 'geoip' . DS . $type . '.json');
	}

}
