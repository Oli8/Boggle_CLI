<?php

class Boggle {

	public $dices = [];
	public $score = ['3' => 1, '4' => 1, '5' => 2, '6' => 3, '7' => 5, '8' => 11];
	public $grid = [];

	public function __construct(){
		$this->dices = self::generateDices();
		$this->generateGrid();
	}

	public static function generateDices(){
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
	}


}

$b = new Boggle;
var_dump($b->grid);