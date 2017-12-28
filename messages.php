<?php

$messages = [
	"welcome" => [
		"en" => "welcome to boggle_cli!",
		"fr" => "bienvenue sur boggle_cli !"
	],
	"words_found" => [
		"en" => "Words found:",
		"fr" => "Mots trouvés :"
	],
	"enter_word" => [
		"en" => "Type a word:",
		"fr" => "Entrez un mot :"
	],
	"time_left" => [
		"en" => "Time remaining:",
		"fr" => "Temps restant :"
	],
	"time_unit" => [
		"en" => "second(s)",
		"fr" => "seconde(s)"
	],
	"elapsed_time" => [
		"en" => "Elapsed time",
		"fr" => "Temps écoulé"
	],
	"new_record" => [
		"en" => "New record!",
		"fr" => "Nouveau record !"
	],
	"enter_name" => [
		"en" => "Enter your name:",
		"fr" => "Entrez votre nom :"
	],
	"empty" => [
		"en" => "You typed an empty string :|",
		"fr" => "Vous avez entré une chaine vide :|"
	],
	"word_repeated" => [
		"en" => "You already typed this word",
		"fr" => "Vous avez déjà entré ce mot"
	],
	"unexisting_word" => [
		"en" => "This word does not exists",
		"fr" => "Ce mot n'existe pas"
	],
	"word_gain" => [
		"en" => function($word, $score){
			return "The word $word gets you $score point" . ($score > 1 ? "s" : "");
		},
		"fr" => function($word, $score){
			return "Le mot $word vous rapporte $score point" . ($score > 1 ? "s" : "");
		}
	],
	"word_not_on_grid" => [
		"en" => "is not on the grid.",
		"fr" => "n'est pas présent sur la grille."
	],
	"details" => [
		"en" => "Score details",
		"fr" => "Détails de vos points"
	],
];
