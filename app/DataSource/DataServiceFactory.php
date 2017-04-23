<?php

namespace YukiMt\DataSource;

use YukiMt\DataSource\Cache\Annotation\UseCache;
use YukiMt\DataSource\Cache\CacheManager;
use Ray\Aop\Pointcut;
use Ray\Aop\Matcher;
use Ray\Aop\Bind;
use Ray\Aop\Compiler;

class DataServiceFactory
{
	const TEMP_DIR = __DIR__.'/../../resource/tmp/';

	static function create(): DataService{
		$pointcut = new Pointcut(
			(new Matcher)->any(),
			(new Matcher)->annotatedWith(UseCache::class),
			[new CacheManager]
		);
		$bind = (new Bind)->bind(DataService::class, [$pointcut]);
		return (new Compiler(self::TEMP_DIR))->newInstance(DataService::class, [], $bind);
	}
}
