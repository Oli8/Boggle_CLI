<?php

require_once 'letter.php';

class Boggle {

	public $dices = [];
	public $letterScore = ['3' => 1, '4' => 1, '5' => 2, '6' => 3, '7' => 5, '8' => 11];
	public $grid = [];
	public $gridObj = [];
	public $time = 5; //180;
	public $score = 0;
	#public $words = []

	public function __construct(){
		$this->dices = self::generateDices();
		$this->generateGrid();
		foreach($this->grid as $line)
			echo join($line) . "\n";

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
			echo "Entrez un mot :\n";
			$word = strtoupper(_readline());
			$remainingTime = round($this->time - (microtime(1) - $this->startTime));
			if($remainingTime < 0)
				break;
			echo "Temps restant : $remainingTime seconde(s)\n";
			if($word && $this->find_word($word, $this->gridObj)){
				echo "Le mot $word vous rapporte " . $this->getScore($word) . " point(s).\n";
				$this->score += $this->getScore($word);
				#$this->words[] = $
			}
			else
				echo "Le mot $word n'est pas présent sur la grille.\n";
			echo "\n";
		}
		echo "Temps écoulé\nScore: $this->score\n";

	}

	public function getScore(String $word): Int{
		if(strlen($word) < 3 )
			return 0;

		return $this->letterScore[min(8, strlen($word))];
	}

	public function find_word(String $word, Array $grid, Array $visited = []): Bool{
	    $letters = array_filter(self::find_letters($grid, $word[0]), function ($l) use($visited){
	        return !in_array($l, $visited);
	    });
	    if(!$letters) return false;
	    if(strlen($word) === 1) return true;

	    foreach($letters as $letter)
	        if($this->find_word(substr($word, 1), $letter->neighbours($this->gridObj), array_merge($visited, [$letter])) === true)
	            return true;
	    
	    return false;
	}

	public static function find_letters(Array $grid, String $letter): Array{
	    $found = [];
	    foreach($grid as $line)
	        foreach($line as $l)
	            if($l->value === $letter)
	                $found[] = $l;

	    return $found;
	}

}

function _readline($text=''){
	if (PHP_OS == 'WINNT') {
	  $line = stream_get_line(STDIN, 1024, PHP_EOL);
	} 
	else
	  $line = readline($text);

	return $line;
}

(new Boggle)->play();
