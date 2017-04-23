<?php

namespace YukiMt\DataSource\Cache\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class UseCache
{
	public function __construct($exclusive){}
}
