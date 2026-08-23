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
 * @package       app.Controller.Component
 * @since         XLRstats v3.0
 * @version       0.1
 */

App::uses('Component', 'Controller');
App::import('Vendor', 'MaxMindDbAutoloader', array('file' => 'MaxMind' . DS . 'bootstrap.php'));

/**
 * Class GeoIPComponent
 *
 * Country level IP lookups against a DB-IP Lite MMDB database
 * (https://db-ip.com). The database can be downloaded and updated
 * from the admin dashboard (Maintenance).
 */
class GeoIPComponent extends Component {

/**
 * MaxMind DB Reader instance or null when no database is available
 *
 * @var null
 */
	public $gi = null;

/**
 * Name of the country level database file inside Configure::read('GeoIP.dbPath')
 *
 * @var string
 */
	public $dbFile = 'dbip-country-lite.mmdb';

//-------------------------------------------------------------------

/**
 * @param Controller $controller
 */
	public function initialize(Controller $controller) {
		$this->gi = $this->_openReader();
	}

//-------------------------------------------------------------------

/**
 * @param Controller $controller
 */
	public function shutdown(Controller $controller) {
		$this->gi = null; // release the file handle
	}

//-------------------------------------------------------------------

/**
 * Returns the ISO 3166-1 alpha-2 country code for an address,
 * '-' when unknown
 *
 * @param null $address
 * @return bool|string
 */
	public function country_code($address = null) {
		$record = $this->_record($address);
		if (!isset($record['country']['iso_code'])) {
			return '-';
		}
		return $record['country']['iso_code'];
	}

//-------------------------------------------------------------------

/**
 * Returns the country name for an address, 'Unknown' when unknown
 *
 * @param null $address
 * @return bool|string
 */
	public function country_name($address = null) {
		$record = $this->_record($address);
		if (!isset($record['country']['names']['en'])) {
			return 'Unknown';
		}
		return $record['country']['names']['en'];
	}

//-------------------------------------------------------------------

/**
 * Opens the MMDB reader, returns null (instead of failing) when the
 * database is not downloaded yet or unreadable
 *
 * @return null|\MaxMind\Db\Reader
 */
	protected function _openReader() {
		$path = $this->_dbPath();
		if (!is_readable($path)) {
			$this->_logMissingOnce('GeoIP.missingCountryDb', 'Country database not found at "' . $path . '". Download it via Dashboard > Maintenance.');
			return null;
		}
		try {
			return new \MaxMind\Db\Reader($path);
		} catch (Exception $e) {
			CakeLog::write('geoip', 'Could not open country database "' . $path . '": ' . $e->getMessage());
			return null;
		}
	}

//-------------------------------------------------------------------

/**
 * Path to this component's database file
 *
 * @return string
 */
	protected function _dbPath() {
		$dir = Configure::read('GeoIP.dbPath');
		if (empty($dir)) {
			$dir = APP . 'Vendor' . DS . 'dbip';
		}
		return $dir . DS . $this->dbFile;
	}

//-------------------------------------------------------------------

/**
 * Looks up an address, returns an empty array on any failure
 *
 * @param null $address
 * @return array
 */
	protected function _record($address) {
		if ($this->gi === null || empty($address)) {
			return array();
		}
		try {
			$record = $this->gi->get($address);
			return is_array($record) ? $record : array();
		} catch (Exception $e) {
			return array();
		}
	}

//-------------------------------------------------------------------

/**
 * Logs a message to the geoip log at most once per day to avoid
 * filling up the logs on every request
 *
 * @param string $cacheKey
 * @param string $message
 */
	protected function _logMissingOnce($cacheKey, $message) {
		$last = Cache::read($cacheKey);
		if ($last !== false && $last > strtotime('-1 day')) {
			return;
		}
		CakeLog::write('geoip', $message);
		Cache::write($cacheKey, time());
	}
}
