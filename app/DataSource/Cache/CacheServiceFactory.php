<?php

namespace YukiMt\DataSource\Cache;

use YukiMt\DataSource\Cache\Annotation\UseCache;
use Ray\Aop\Pointcut;
use Ray\Aop\Matcher;
use Ray\Aop\Bind;
use Ray\Aop\Compiler;

class CacheServiceFactory
{
	const TEMP_DIR = __DIR__.'/../../../resource/tmp/';

	static function create($className, $args = []) {
		$pointcut = new Pointcut(
			(new Matcher)->any(),
			(new Matcher)->annotatedWith(UseCache::class),
			[new CacheManager]
		);
		$bind = (new Bind)->bind($className, [$pointcut]);
		return (new Compiler(self::TEMP_DIR))->newInstance($className, $args, $bind);
	}
}
