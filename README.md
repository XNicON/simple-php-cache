# Simple PHP Cache
## Quick Start
To install Simple PHP Cache, simply:

    $ composer require x-nicon/php-simple-cache

## Code Examples
### Example One
```php
require __DIR__ . '/vendor/autoload.php';

use \phpCache\Cache;

$c = new Cache();

if(!$c->has("example")) {
  $c->set("example", 'cache data', 300); // cache result for 5 minutes (300 seconds)
} else {
  echo $c->get("example");
}

```

### Example Two
```php
$example = $c->get("example");

if($example !== false) {
  echo "Cached: " . $example;
  $c->remove("example"); // remove from cache
} else {
  echo "Cache not found or expired";
}
```

## Functions
### __construct($name, $dir, $ext)
Class constructor
* `$name` - name of the cache (default phpcache)
* `$dir` - directory where the cache will be stored (default TEMP system)
* `$ext` - extension of the cache file (default .cache)

### set($key, $value, $ttl = -1)
Writes data to cache
* `$key` - key of the value
* `$value` - value
* `$ttl` - *Time To Live* (in how many seconds value will expire)

### get($key, &$out)
Reads data from cache
* `$key` - key of the value
* return:
  * bool(false) - value not cached or expired
  * array - if success
  
### remove($key)
Removes data from cache
* `$key` - key of the value
* return:
  * bool(false) - key not found
  * bool(true) - success
  
### has($key)
Check exists and not is expired cache
* `$key` - key of the value
* return:
  * bool(false) - not found
  * bool(true) - is found

### clean()
clean cache file
* return:
  * bool(false) - fail
  * bool(true) - success