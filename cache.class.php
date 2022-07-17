<?php

/**
 * @copyright MIT
 * @author    X-NicON https://github.com/X-NicON
 * @since     0.2
 */
namespace phpCache;

use RuntimeException;

class Cache
{
    private array $cache = [];
    private string $path;

    public function __construct($name = 'phpcache', $dir = null, $ext = '.cache')
    {
        $dir = $dir ?? sys_get_temp_dir();

        if (!is_dir($dir) && !mkdir($dir, 0775, true)) {
            throw new RuntimeException('Unable to create cache directory (' . $dir . ')');
        }

        if (!is_readable($dir) || !is_writable($dir)) {
            if (!chmod($dir, 0775)) {
                throw new RuntimeException('Cache directory must be readable and writable (' . $dir . ')');
            }
        }

        $this->path = $dir . '/' . $name . $ext;

        if (file_exists($this->path)) {
            $file = file_get_contents($this->path);
            if (!empty($file)) {
                $this->cache = unserialize($file);
            }
        }
    }

    public function __destruct()
    {
        $this->saveStore();
    }

    public function set($key, $value, $ttl = 0)
    {
        if ($ttl > 0) {
            $ttl += time();
        }

        $this->cache[$key] = [
            'e' => $ttl,
            'v' => $value
        ];
    }

    public function get($key, $default = false)
    {
        if ($this->has($key)) {
            return $this->cache[$key]['v'];
        }

        return $default;
    }

    public function remove($key): bool
    {
        if (array_key_exists($key, $this->cache)) {
            unset($this->cache[$key]);

            return true;
        }

        return false;
    }

    public function has($key): bool
    {
        if (array_key_exists($key, $this->cache)) {
            if ($this->cache[$key]['e'] === 0 || $this->cache[$key]['e'] > time()) {
                return true;
            }

            unset($this->cache[$key]);
        }

        return false;
    }

    public function clean()
    {
        $this->cache = [];
        return $this->saveStore();
    }

    private function saveStore(): bool
    {
        return (bool)file_put_contents($this->path, serialize($this->cache));
    }
}
