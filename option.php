<?php

Class Option {

	public $short, $long, $callback;

	public function __construct(String $short, String $long, Closure $callback){
		$this->short = $short;
		$this->long = $long;
		$this->callback = $callback;
	}

	public function check($context = null){
		$opt = getopt($this->short, [$this->long]);
		$short_name = $this->short[0];
		$long_name = substr($this->long, 0, strrpos($this->long, ':'));

		if(isset($opt[$short_name]) || isset($opt[$long_name]))
			$this->callback->call($context ?: $this, $opt[$short_name] ?? $opt[$long_name]);
	}

}

$boogle = new Class{};

$options = [
	new Option('t:', 'time:', function($val){
		$this->time = intval($val);
	}),
];

var_dump($boogle);
# handle options
// foreach($options as $opt)
// 	$opt->check($boogle);

#current($options)->check($boogle);
call_user_func_array([current($options), 'check'], [$boogle]);

var_dump($boogle);
