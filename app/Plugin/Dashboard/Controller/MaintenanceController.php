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
 * @param null $type
 */
	public function admin_geoDbUpdate($type = null) {
		if (!isset($this->_geoDatabases[$type])) {
			$this->Session->setFlash(__('Unknown database type.'), 'default', array('class' => 'message warning'));
			return $this->redirect(array('action' => 'admin_geoDb'));
		}

		$config = $this->_geoDatabases[$type];
		set_time_limit(0);

		$dir = $this->_geoDbDir();
		if (!is_dir($dir)) {
			mkdir($dir, 0775, true);
		}
		if (!is_writable($dir)) {
			CakeLog::write('geoip', 'Geo IP database directory "' . $dir . '" is not writable.');
			$this->Session->setFlash(__('The database directory is not writable by the web server.'), 'default', array('class' => 'message error'));
			return $this->redirect(array('action' => 'admin_geoDb'));
		}

		// Download: try this month's release first, then last month's.
		$gzPath = $dir . DS . 'download.tmp.gz';
		$error = '';
		foreach (array(date('Y-m'), date('Y-m', strtotime('-1 month'))) as $month) {
			$url = sprintf($config['url'], $month);
			if ($this->_downloadFile($url, $gzPath)) {
				$error = '';
				break;
			}
			$error = 'Download failed: ' . $url;
			@unlink($gzPath);
		}
		if (!empty($error)) {
			CakeLog::write('geoip', $error);
			$this->Session->setFlash(__('Could not download the %s database. Check the error log for details.', $config['label']), 'default', array('class' => 'message error'));
			return $this->redirect(array('action' => 'admin_geoDb'));
		}

		if (!$this->_installGzipDatabase($gzPath, $dir . DS . $config['file'])) {
			$this->Session->setFlash(__('Downloaded %s database file appears to be invalid.', $config['label']), 'default', array('class' => 'message error'));
			return $this->redirect(array('action' => 'admin_geoDb'));
		}

		CakeLog::write('geoip', ucfirst($config['label']) . ' database updated successfully.');
		$this->Session->setFlash(__('%s database installed successfully.', $config['label']));
		return $this->redirect(array('action' => 'admin_geoDb'));
	}

//-------------------------------------------------------------------

/**
 * Streams a URL to a local file using cURL when available,
 * falling back to HttpSocket otherwise.
 *
 * @param string $url
 * @param string $target
 * @return bool
 */
	protected function _downloadFile($url, $target) {
		if (function_exists('curl_init')) {
			$handle = @fopen($target, 'wb');
			if ($handle === false) {
				return false;
			}
			$curl = curl_init($url);
			curl_setopt_array($curl, array(
				CURLOPT_FILE => $handle,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_MAXREDIRS => 5,
				CURLOPT_CONNECTTIMEOUT => 15,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FAILONERROR => true,
				CURLOPT_USERAGENT => 'XLRstats webfront v3',
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
 * @return bool
 */
	protected function _installGzipDatabase($gzPath, $target) {
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
		while (!gzeof($source)) {
			fwrite($dest, gzread($source, 65536));
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
 * Directory holding the geo IP databases
 *
 * @return string
 */
	protected function _geoDbDir() {
		$dir = Configure::read('GeoIP.dbPath');
		if (empty($dir)) {
			$dir = APP . 'Vendor' . DS . 'dbip';
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

}
