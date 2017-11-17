<?php

class Letter {
    public $value = "";
    public $coords = [];
    
    public function __construct(String $value, Array $coords){
        $this->value = $value;
        $this->coords = $coords;
    }
    
    public function neighbours(Array $grid):Array{
        $adj = [];
        foreach ([[-1, -1], [-1,  0], [-1,  1], [ 0, -1], [ 0,  1], [ 1, -1], [ 1,  0], [ 1,  1]] as $c){
            if(isset($grid[$this->coords[0] + $c[0]][$this->coords[1] + $c[1]])){
                $adj[] = [$grid[$this->coords[0] + $c[0]][$this->coords[1] + $c[1]]];
            }
        }
        return $adj;
    }
}