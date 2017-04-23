<?php

namespace YukiMt\DataSource\Cache;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class CacheManager implements MethodInterceptor
{
	private $cache; // KVS (such as Redis, Memcache) should be used in the real world
	private $useCacheKey;
	private $saveCacheKey;

	public function __construct(){
		$this->cache = array();
		$this->useCacheKey = 'useCache';
		$this->saveCacheKey = 'saveCache';
	}

	public function invoke(MethodInvocation $invocation){
		$args = $this->getArguments($invocation);
		$cacheKey = $this->getCacheKey($invocation, $args);

		//get cache if exists
		if(!array_key_exists($this->useCacheKey, $args) || $args[$this->useCacheKey]){
			$cache = $this->getCache($cacheKey);
			if(isset($cache))
				return $cache;
		}
		
		//execute the original method
		$result = $invocation->proceed();

		//update cache
		if(!array_key_exists($this->saveCacheKey, $args) || $args[$this->saveCacheKey]){
			$this->setCache($cacheKey, $result);
		}

		return $result;
	}

	private function getCache(string $key){
		if(array_key_exists($key, $this->cache)){
			return $this->cache[$key];
		} else {
			return null;
		}
	}

	private function setCache(string $key, $value){
		$this->cache[$key] = $value;
	}

	private function getCacheKey(MethodInvocation $invocation, array $args): string{
		$className = get_class($invocation->getThis());
		$method = $invocation->getMethod();
		$methodName = $method->getShortName();
		$exclusives = $this->getExclusiveParams($method);
		foreach ($exclusives as $exclusive) {
			if(array_key_exists($exclusive, $args))
				unset($args[$exclusive]);
		}
		$argKey = implode('_', $args);
		return "{$className}_{$methodName}_{$argKey}";
	}

	private function getArguments(MethodInvocation $invocation): array{
		$params = $invocation->getMethod()->getParameters();
		$args = $invocation->getArguments();
		$result = array();
		foreach($params as $index => $param){
			$result[$param->getName()] = $args[$index];
		}
		return $result;
	}

	private function getExclusiveParams(\ReflectionMethod $method): array{
		$params = array($this->useCacheKey, $this->saveCacheKey);
		preg_match('/@UseCache\((exclusive=)?"(.+)"\)/', $method->getDocComment(), $match);
		if(count($match) > 2){
			return array_merge($params, explode(',', $match[2]));
		} else {
			return $params;
		}
	}
}
