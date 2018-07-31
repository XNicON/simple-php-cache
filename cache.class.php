<?php

/**
 * cache.class.php
 *
 * @copyright      MIT
 * @author         X-NicON https://github.com/X-NicON
 * @since          0.1
 *
 */

namespace phpCache;

class Cache {

  /**
   * @var array
   */
	private $cache = [];

	/**
	 * @var string
	 */
	private $dir;

  /**
   * @var string
   */
	private $path;

	function __construct($name = 'phpcache', $dir = null, $ext = '.cache') {
		if($dir == null) {
			$this->dir = sys_get_temp_dir();
		} else {
			$this->dir = $dir;
		}

    if(!is_dir($this->dir) && !mkdir($this->dir, 0775, true)) {
      throw new Exception('Unable to create cache directory ('.$this->dir.')');
    }

    if(!is_readable($this->dir) || !is_writable($this->dir)) {
      if(!chmod($this->dir, 0775)) {
        throw new Exception('Cache directory must be readable and writable ('.$this->dir.')');
      }
    }

		$this->path = $this->dir.'/'.$name.$ext;

		if(file_exists($this->path)) {
			$file = file_get_contents($this->path);
			if(!empty($file)) {
				$this->cache = unserialize($file);
			}
		}
	}

	function __destruct() {
		$this->savestore();
	}

	public function set($key, $value, $ttl = -1) {
		$this->cache[$key]['e'] = time()+$ttl;
		$this->cache[$key]['v'] = $value;
	}

	public function get($key) {
		if($this->has($key)) {
			return $this->cache[$key]['v'];
		}

		return false;
	}

	public function remove($key) {
		if(array_key_exists($key, $this->cache)) {
			unset($this->cache[$key]);
			return true;
		}

		return false;
	}

	public function has($key) {
		if(array_key_exists($key, $this->cache)) {
			if($this->cache[$key]['e'] == -1 || $this->cache[$key]['e'] > time()) {
				return true;
			} else {
				unset($this->cache[$key]);
			}
		}

		return false;
	}

	public function clean() {
		$this->cache = [];
		return $this->savestore();
	}

	private function savestore() {
		return (bool)file_put_contents($this->path, serialize($this->cache));
	}

}