<?php
require_once __DIR__.'/../vendor/autoload.php';

use YukiMt\DataSource\Cache\CacheServiceFactory;
use YukiMt\DataSource\DataService;

$db = CacheServiceFactory::create(DataService::class);

while(true){
	echo "\n";

	$method = getStdin('getById or updateById?', ['g', 'u']);
	$id = (int)getStdin('which ID?', ['1', '2']);

	if($method == 'u'){
		$score = (float)getStdin('new score');
		if($db->updateById($id, $score)){
			echo "Successfully updated to $score\n";
		} else {
			echo "Failed to update...\n";
		}
	} else {
		$useCache = getStdin('use cache?', ['y', 'n']);
		$useCache = $useCache == 'y';
		$saveCache = getStdin('save cache?', ['y', 'n']);
		$saveCache = $saveCache == 'y';
		$score = $db->getById($id, $useCache, $saveCache)['score'];
		echo "You got $score.\n";
	}
}

function getStdin($msg, $options = []){
	if(empty($options)){
		$optionStr = '';
	} else {
		$optionStr = '('.implode("/", $options).')';
	}
	do{
		echo "$msg$optionStr: ";
		$stdin = trim(fgets(STDIN));
	} while(!empty($options) && !in_array($stdin, $options));
	return $stdin;
}
