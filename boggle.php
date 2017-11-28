<?php

require_once 'letter.php';

class Boggle {

	public $dices = [];
	public $letterScore = ['3' => 1, '4' => 1, '5' => 2, '6' => 3, '7' => 5, '8' => 11];
	public $grid = [];
	public $gridObj = [];
	public $time = 30; //180;
	public $score = 0;
	private $words = [];

	public function __construct(){
		echo self::_print(strtoupper(self::header("welcome to boggle_cli!")), "success");
		sleep(2);
		$this->dices = self::generateDices();
		$this->generateGrid();
		$this->startTime = microtime(1);
	}

	public static function generateDices(): Array{
		$dices = ["LENUYG", "ELUPST", "ZDVNEA", "SDTNOE", "AMORIS", "FXRAOI", "MOQABJ", "FSHEEI", "HRSNEI", "ETNKOU", "TARILB",
		 "TIEAOA", "ACEPDM", "RLASEC", "ULIWER", "VGTNIE"];

		return array_map(function($d){
			return str_split($d);
		}, $dices);
	}

	public function generateGrid(){
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
			echo count($this->words) ? "Mots trouvés : " . join(', ', $this->words) . "\n" : '';
			echo "Entrez un mot :\n";

			$word = strtoupper(_readline());
			$remainingTime = round($this->time - (microtime(1) - $this->startTime));
			if($remainingTime < 0)
				break;

			$remainingTime_str = "Temps restant : $remainingTime seconde(s)\n";
			if($remainingTime < 10)
				echo self::_print($remainingTime_str, "danger");
			else
				echo $remainingTime_str;

			$this->handle_word($word);
			echo "\n";
			sleep(2);
		}
		echo self::_print("Temps écoulé", "danger");
		echo self::_print("Score: $this->score", "success");
		$this->game_info();
	}

	private function handle_word(String $word){
		if(!$word)
			echo self::_print("Vous avez entré une chaine vide :|", "warning");
		else if(in_array($word, $this->words))
			echo self::_print("Vous avez déjà entré ce mot", "warning");
		else if(!self::valid_word($word))
			echo self::_print("Ce mot n'existe pas", "warning");
		else if($this->find_word($word, $this->gridObj)){
			$score = $this->getScore($word);
			echo self::_print("Le mot $word vous rapporte $score point" . ($score > 1 ? "s" : ""), "success");
			$this->score += $score;
			$this->words[] = $word;
		}
		else
			echo self::_print("Le mot $word n'est pas présent sur la grille.", "warning");
	}

	private static function valid_word(String $word): Bool{
		return in_array($word, array_map('trim', file('french_words.txt')));
	}

	public function getScore(String $word): Int{
		if(strlen($word) < 3 )
			return 0;

		return $this->letterScore[min(8, strlen($word))];
	}

	public function find_word(String $word, Array $grid, Array $visited = []): Bool{
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
		usort($this->words, function($a, $b){
			return strlen($b) <=> strlen($a);
		});
		if($this->score > 0){
			echo self::header("Détails de vos points");
			echo self::_print(join("\n", array_map(function($w){
				return $w . " -> " . $this->getScore($w);
			}, $this->words)), "success");
		}
	}

	public static function find_letters(Array $grid, String $letter): Array{
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

	public function displayGrid(){
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
		return "$border\n$msg\n$border\n";
	}

}

function _readline(String $text=''): String{
	if (PHP_OS == 'WINNT') {
	  $line = stream_get_line(STDIN, 1024, PHP_EOL);
	} 
	else
	  $line = readline($text);

	return $line;
}

(new Boggle)->play();
