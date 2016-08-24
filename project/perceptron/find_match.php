<?php
/****
Author: André Paterlini Oliveira Vieira (and_pater@uol.com.br)
Date: June 05 2004
Institution: Mackenzie - Computer and Informatics College (www.mackenzie.com.br)
Instructor(teacher): Antonio Luiz Basile

The following PHP script shows a real implementation of a RNA Perceptron.

The object of this script is to find a class pattern from the submited user profile (see index.php - HTML form)
and find a match for it consulting an marriage agency people database.

Remember this algorithm is for educational use!!

****/

	//Declarates classes and instanciate it
	include("perceptron_class.php");
	$rna_class = new perceptron();
	
	//Setup Debug Mode
	$rna_class->debug = true;
	
	//Defines a training input data
	$training_array = array(
		array(
			"x1" => $_POST["p1"], "x2" => $_POST["p2"], "x3" => $_POST["p3"], "x4" => $_POST["p4"], "o" => 1 
		),
		array(
			"x1" => (-1 * $_POST["p1"]), "x2" => (-1 * $_POST["p2"]), "x3" => (-1 * $_POST["p3"]), "x4" => (-1 * $_POST["p4"]), "o" => -1 
		)
	);

	//Train the RNA
	$train_result = $rna_class->train($training_array, 1, 1);
	
	/*************** TESTS ***************/
	
	//Defines a set of tests
		$testing_array["male"] = array(
		"João" => array(
			"x1" => 1, "x2" => 1, "x3" => -1, "x4" => 1 
		),
		"Pedro" => array(
			"x1" => 1, "x2" => 1, "x3" => -1, "x4" => -1 
		),
		"José" => array(
			"x1" => 1, "x2" => -1, "x3" => 1, "x4" => 1 
		),
		"Mané" => array(
			"x1" => 1, "x2" => -1, "x3" => -1, "x4" => 1 
		),
		"Vilmar" => array(
			"x1" => 1, "x2" => -1, "x3" => 1, "x4" => 1 
		),
		"Flavio" => array(
			"x1" => -1, "x2" => -1, "x3" => 1, "x4" => 1 
		),
		"Paulo" => array(
			"x1" => 1, "x2" => -1, "x3" => -1, "x4" => -1 
		)
	);

	$testing_array["female"] = array(
		"Maria" => array(
			"x1" => -1, "x2" => -1, "x3" => 1, "x4" => -1 
		),
		"Karina" => array(
			"x1" => -1, "x2" => -1, "x3" => -1, "x4" => -1 
		),
		"Catia" => array(
			"x1" => -1, "x2" => -1, "x3" => 1, "x4" => 1 
		),
		"Flavia" => array(
			"x1" => -1, "x2" => 1, "x3" => 1, "x4" => 1 
		)
	);
	
	// Now tests the database ($testing_array data) against the trainned network
	$matchs = array();
	foreach($testing_array[$_POST["sex"]] as $name => $subject_data) {
		if ($rna_class->test_class($subject_data, $train_result) == true) {
			$matchs[] = $name;
		}
	}
	
	// Prints the names of people that fit the same class in wich the user input was applied to
	echo "The system found " . count($matchs) . " person(s) that match with your profile: " . implode(", ", $matchs) . ".";
?>