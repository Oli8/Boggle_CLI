<?php

require_once 'letter.php';
require_once 'option.php';
require_once 'messages.php';

class Boggle {

	private $dices = [];
	private $grid = [];
	private $gridObj = [];
	private $time = 180;
	private $score = 0;
	private $words = ['valid' => [], 'invalid' => []];
	private $malus = false;
	private $lang = 'en';

	public function __construct(){
		global $options;
		system('clear');
		$this->handle_options($options);
		echo self::_print(strtoupper(self::header($this->message("welcome"))), "success");
		sleep(2);
		$this->dices = self::generateDices();
		$this->generateGrid();
		$this->startTime = microtime(1);
	}

	private static function generateDices(): Array{
		$dices = ["LENUYG", "ELUPST", "ZDVNEA", "SDTNOE", "AMORIS", "FXRAOI", "MOQABJ", "FSHEEI", "HRSNEI", "ETNKOU", "TARILB",
		 "TIEAOA", "ACEPDM", "RLASEC", "ULIWER", "VGTNIE"];

		return array_map('str_split', $dices);
	}

	private function generateGrid(){
		shuffle($this->dices);
		$grid = array_map(function ($die){
			return $die[rand(0, 5)];
		}, $this->dices);

		$this->grid = array_chunk($grid, 4);

		foreach($this->grid as $y => $line)
		    foreach($line as $x => $value)
		        $this->gridObj[$y][$x] = new Letter($value, [$y, $x]);
	}

	public function play(){
		while(1){
			system('clear');
			$this->displayGrid();
			$this->last_word_letters = '';
			echo count($this->words['valid']) ? $this->message("words_found") . join(', ', $this->words['valid']) . "\n" : '';
			echo $this->message("enter_word") . "\n";

			$word = strtoupper(_readline());
			$remainingTime = round($this->time - (microtime(1) - $this->startTime));
			if($remainingTime < 0)
				break;

			$remainingTime_str = "{$this->message('time_left')} $remainingTime {$this->message('time_unit')}\n";
			if($remainingTime < 10)
				echo self::_print($remainingTime_str, "danger");
			else
				echo $remainingTime_str;

			$this->handle_word($word);
			echo "\n";
			sleep(2);
		}
		echo self::_print($this->message("elapsed_time"), "danger");
		echo self::_print("Score: $this->score", "success");
		if($this->words['valid'] || ($this->words['invalid'] && $this->malus))
			$this->game_info();
		$this->check_highscores();
	}

	private function check_highscores(){
		$highscore_file = 'highscores.json';
		$scores = json_decode(@file_get_contents($highscore_file), true) ?: [];
		if(!isset($scores[$this->time]) || $this->score > $scores[$this->time]['score']){
			echo self::_print($this->message("new_record"), "success");
			echo $this->message("enter_name") . "\n";
			$scores[$this->time] = ['player' => trim(readline()) ?: 'Player', 'score' => $this->score];
		}
		file_put_contents($highscore_file, json_encode($scores));

		echo self::header("Record:");
		krsort($scores);
		foreach($scores as $time => $record)
			echo "{$time}s. -> {$record['score']} - {$record['player']}\n";
	}

	private function handle_word(String $word){
		$malus = false;
		if(!$word)
			echo self::_print($this->message("empty"), "warning");
		else if(in_array($word, array_merge($this->words['valid'], $this->words['invalid'])))
			echo self::_print($this->message("word_repeated"), "warning");
		else if(!self::valid_word($word)){
			echo self::_print($this->message("unexisting_word"), "warning");
			$malus = true;
		}
		else if($this->find_word($word, $this->gridObj)){
			$score = $this->getScore($word);
			echo self::_print($this->message("word_gain")($word, $score), "success");
			$this->score += $score;
			$this->words['valid'][] = $word;
		}
		else
			echo self::_print("'$word' " . $this->message("word_not_on_grid"), "warning");

		if($malus){
			$this->words['invalid'][] = $word;
			if($this->malus)
				$this->score -= $this->getScore($word);
		}
	}

	private function valid_word(String $word): Bool{
		return in_array($word, array_map('trim', file("words/{$this->lang}.txt")));
	}

	private static function getScore(String $word): Int{
		if(strlen($word) < 3)
			return 0;

		return ['3' => 1, '4' => 1, '5' => 2, '6' => 3, '7' => 5, '8' => 11][min(8, strlen($word))];
	}

	private function find_word(String $word, Array $grid, Array $visited = []): Bool{
	    $letters = array_filter(self::find_letters($grid, $word[0]), function($l) use($visited){
	        return !in_array($l, $visited);
	    });
	    if(!$letters) return false;
		if(strlen($word) === 1){
			$this->last_word_letters = array_merge($visited, [end($letters)]);//set this if word found
			return true;
		}

	    foreach($letters as $letter)
	        if($this->find_word(substr($word, 1), $letter->neighbours($this->gridObj), array_merge($visited, [$letter])) === true)
				return true;
	    
	    return false;
	}

	private function game_info(){
		$this->words = array_map(function($words){
			usort($words, function($a, $b){
				return strlen($b) <=> strlen($a);
			});
			return $words;
		}, $this->words);

		echo self::header($this->message("details"));
		echo self::_print(join("\n", array_map(function($w){
			return $w . " -> " . $this->getScore($w);
		}, $this->words['valid'])), "success");

		if($this->malus)
			echo self::_print(join("\n", array_map(function($w){
				return $w . " -> " . -$this->getScore($w);
			}, $this->words['invalid'])), "danger");
	}

	private static function find_letters(Array $grid, String $letter): Array{
	    $found = [];
	    foreach($grid as $line)
	        foreach($line as $l)
	            if($l->value === $letter)
	                $found[] = $l;

	    return $found;
	}

	private static function _print(String $msg, String $type, Bool $carriage_return = true): String{
		$colors = [
			'danger' => '0;31',
			'warning' => '0;33',
			'success' => '0;32',
		];

		return "\033[{$colors[$type]}m$msg\033[0m" . str_repeat("\n", !!$carriage_return);
	}

	private function displayGrid(){
		if(!empty($this->last_word_letters))//if not empty -> display word
			foreach($this->gridObj as $line){
				foreach($line as $l)
					echo in_array($l, $this->last_word_letters) ? self::_print($l->value, 'success', false) : $l->value;
				echo "\n";
			}
		else
			foreach($this->grid as $line)
				echo join($line) . "\n";
	}

	private static function header(String $msg): String{
		$border = str_repeat("*", strlen($msg));
		return "\n$border\n$msg\n$border\n";
	}

	private function handle_options($options){
		foreach($options as $opt)
			$opt->check($this);
	}

	private static function display_help(){
		return self::header("BOGGLE_CLI") . "\n" . join("\n", [
			"Usage: php boogle.php [options]\n",
			"-t, --time TIME  Set time to TIME",
			"-m, --malus      Enable malus",
			"-h --help        Display help",
			"-l, --lang LANG  Set language to LANG",
			""]);
	}

	private function message($key){
		global $messages;
		return $messages[$key][$this->lang];
	}

}

function _readline(String $text=''): String{
	$line = PHP_OS == 'WINNT' ? stream_get_line(STDIN, 1024, PHP_EOL) : readline($text);

	return $line;
}

$options = [
	new Option('m', 'malus', function(){
		$this->malus = true;
	}),
	new Option('h', 'help', function(){
		die(self::display_help());
	}),
	new Option('t:', 'time:', function($val){
		$this->time = $val;
	}),
	new Option('l:', 'lang:', function($val){
		$this->lang = $val;
	}),
];

(new Boggle)->play();
