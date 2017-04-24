# AOP-Cache
## What is it
sample code to demonstrate cache control by using [Ray.aop](https://github.com/ray-di/Ray.Aop) (Aspect Oriented)

## Demo
```
$ composer install
$ composer dump-autoload
$ php app/index.php

getById or updateById?(g/u): g
which ID?(1/2): 1
use cache?(y/n): y
save cache?(y/n): y   # <- save cache
You got 100.

getById or updateById?(g/u): u
which ID?(1/2): 1
new score: 340        # <- updated score in Database (such as MySQL)
Successfully updated to 340

getById or updateById?(g/u): g
which ID?(1/2): 1
use cache?(y/n): y
save cache?(y/n): y
You got 100.          # <- score in Database is 340, but cache still has the old score

getById or updateById?(g/u): g
which ID?(1/2): 1
use cache?(y/n): n    # <- not use cache
save cache?(y/n): y   # <- update cache to the current score in Database
You got 340.

getById or updateById?(g/u): g
which ID?(1/2): 1
use cache?(y/n): y
save cache?(y/n): y
You got 340.          # <- score in cache is also 340 now

```
## How to use
Refer: [DataService](https://github.com/yuki-mt/aop-cache/blob/master/app/DataSource/DataService.php)

By adding `@UseCache` annotation, your method is replaced with [invoke](https://github.com/yuki-mt/aop-cache/blob/master/app/DataSource/Cache/CacheManager.php#L20) method of `CacheManager`.

([Here](https://github.com/yuki-mt/aop-cache/blob/master/app/DataSource/Cache/CacheServiceFactory.php#L15), your class (in this sample code, `DataService` class), `UseCache` annotation, and `CacheManager` are binded.

```
use YukiMt\DataSource\Cache\Annotation\UseCache;

/* ... */

/**
 * @UseCache
 */
function myMethod(/*...*/){
    // the return value of this method will be saved to cache
    // netx time, the method is not executed, and just return the cached value
}
```

You can choose if you use cache and save cache by adding parameters to your method like below.

(If your method does not have `$saveCache` `$useCache`, automatically use and save cache)

```
/**
 * @UseCache
 */
function myMethod($saveCache, $useCache){
    // control to use and save cache by arguments
}
```

### Cache key
#### the basic form of cache key: 

```
<class name>_<function name>_<arg1>_<arg2>_...
```

e.g.

```
use YukiMt\DataSource\Cache\Annotation\UseCache;
use YukiMt\DataSource\Cache\CacheServiceFactory;

class A
{
	/**
	 * @UseCache
	 */
	function myMethod($myArg1, $myArg2, $useCache){
		//...
	}
}

$a = CacheServiceFactory::create(A::class);
$a->myMethod("aop", 34, true);
```

The cache key of the method above:

(`$useCache`, `$saveCache` parameters are ignored in default)

```
A_myMethod_aop_34
```

**all arguments needs to be able to convert into string.**

so if you use a class in your method, the class needs to have `__toString()` method.

#### exclude some parameters
e.g.

```
class A
{
	/**
	 * @UseCache(exclusive="myArg1,myArg3")
	 */
	function myMethod($myArg1, $myArg2, $useCache, $myArg3){
		//...
	}
}

$a = CacheServiceFactory::create(A::class);
$a->myMethod("aop", 34, true, "hoge");
```

The cache key of the method above:

(You can exclude parameters that you do not want to use as cache key)

```
A_myMethod_34
```

detail is [here](https://github.com/yuki-mt/aop-cache/blob/master/app/DataSource/Cache/CacheManager.php#L54)
