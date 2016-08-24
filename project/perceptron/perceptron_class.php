<?php
/****
Author: André Paterlini Oliveira Vieira (and_pater@uol.com.br)
Date: June 05 2004
Institution: Mackenzie - Computer and Informatics College (www.mackenzie.com.br)
Instructor(teacher): Antonio Luiz Basile

The object of this class is to determinate an pattern given an input (neural terminal signals). For example, supose you like to simulate a palmtop pen pad character detection. What makes a palmtop understand your writting for a given word, is a neural network (perceptron) that will guess that you draw an "A", for example, because a initial "A" pattern was submited to its neural network. The purpose of this PHP class is just alike.

Class attributes:
- w(Array): This represents the model weigths, in an RNA Perceptron, each input will have its corresponding weigth after the net is trained. As for the class it is used as an array of weigths to be calculated by the perceptron training function.
- dw(Array): This represents the weigths variation inside the Perceptron algorithm model, each weigth has its corresponding delta W (weigth variation). Just like on w, this is an array that will be used on the Percetron training function.
- debug(Boolean): This is a flag that tells the class either to print debug messages or not.

Class methods:
- initialize(): This is an intrenal function to initialize the RNA network variables (weights etc). It is used on train method.
- train(): This is the heart of the class. This fuction will train an Perceptron network given its inputs and its desirable output. The return of this function is an array of weigths to shows the network pattern. The returning pattern array will be used by test_class method to test an input against the network.
- test_class(): This is method you will use to test a given input against the trained network.

****/

class perceptron {

	//Define classes parameters
	var $w = array();
	var $dw = array();
	//Debug flag
	var $debug = false;

	//Initialization function
	//usage : void initialize(int n_columns)
	//argument : n_columns >> Number of columns (inputs) that are been used on the perceptron
	//return : void
	function initialize($n_columns) {
	
		//Initialize perceptron vars
		for($i = 1; $i <= $n_columns; $i++) {
			//weigth vars
			$this->w[$i] = 0;
			$this->dw[$i] = 0;
		}
		
	}// END - perceptron
			
	//Calculates the ouput value
	//usage : array train(array input_array, int alpha, int teta)
	//arguments:
	//- input_array
	//	Each line of input_array is an input row and each column of each row is an input value
	//	Ex: A test of a subject containing 2 rows and 4 columns + 1 desired output value
	//	input_array = array(
	//		0 => array(
	//			"x1" => 1, "x2" => -1, "x3" => 1, "x4" => -1, "xN" => 1, "o" => 1 
	//		),
	//		1 => array(
	//			"x1" => 1, "x2" => -1, "x3" => 1, "x4" => -1, "xN" => -1, "o" => -1
	//		)
	//	)
	//- alpha
	//	Artificial Neural Network Learning Rate
	//- teta
	//	Artificial Neural Network Threshold
	//return : array
	function train($input_array, $alpha, $teta) {
		
		//Defines local vars
		$last_w1 = 0;
		$last_w2 = 0;
		$last_w3 = 0;
		$last_w4 = 0;
		$Yin = 0;
		$checkpoint_arr = array();
		$keep_trainning = true;

		//Initialize RNA vars
		$this->initialize(count($input_array[0]) - 1);
		$just_started = true;
		$training_times = 0;
		
		//Trains RNA until it gets stable
		while ($keep_trainning == true) {
			
			//Sweeps each row of the input subject
			foreach($input_array as $row_counter => $row_data) {

				//Finds out the number of columns the input has
				$n_columns = count($row_data) - 1;
				
				//Calculates Yin
				$Yin = 0;
				for($i = 1; $i <= $n_columns; $i++) {
					$Yin += $row_data["x".$i] * ${"last_w".$i};
				}

				//Calculates Real Output
				$Y = ($Yin <= 1) ? -1 : 1;
					
				//Sweeps columns ...
				$checkpoint_arr[$row_counter] = 0;	
				for($i = 1; $i <= $n_columns; $i++) {
					/*** DELTAS ***/
					//Is it the first row??
					if ($just_started == true) {
						$this->dw[$i] = ${"last_w".$i};
						$just_started = false;
					//Found desired output??
					} elseif ($Y == $row_data["o"]) {
						$this->dw[$i] = 0;
					//Calculates Delta Ws
					} else {
						$this->dw[$i] = $row_data["x".$i] * $row_data["o"];
					}
					/*** WEIGTHS ***/
					// Caltulate Weigths
					$this->w[$i] = $this->dw[$i] + ${"last_w".$i};
					${"last_w".$i} = $this->w[$i];
					/*** CHECK-POINT ***/
					$checkpoint_arr[$row_counter] += $this->w[$i];
				}//	END - for
				
				foreach($this->w as $index => $w_item) {
					$debug_w["W".$index] = $w_item;
					$debug_dw["deltaW".$index] = $this->dw[$index];
				}
				
				//Special for script debuging
				$debug_vars[] = array_merge($row_data, array("Bias" => 1, "Yin" => $Yin, "Y" => $Y), $debug_dw, $debug_w, array("deltaBias" => 1));
												
			}// END - foreach
			
			//Special for script debuging
			$empty_data_row = array();
			for($i = 1; $i <= $n_columns; $i++) {
				$empty_data_row["x".$i] = "--";
				$empty_data_row["W".$i] = "--";
				$empty_data_row["deltaW".$i] = "--";
			}
			$debug_vars[] = array_merge($empty_data_row, array("o" => "--", "Bias" => "--", "Yin" => "--", "Y" => "--", "deltaBias" => "--"));
			
			//Counts training times 
			$training_times++;
			
			//Now checks if the RNA is stable already
			$referer_value = end($checkpoint_arr);
			//if all rows match the desired output ...
			$sum = array_sum($checkpoint_arr);
			$n_rows = count($checkpoint_arr);
			if ($training_times > 1 && ($sum / $n_rows) == $referer_value) {
				$keep_trainning = false;
			}
			
		}// END - while
		
		//Prepares the final result
		$result = array();
		for($i = 1; $i <= $n_columns; $i++) {
			$result["w".$i] = $this->w[$i];
		}
		
		$this->debug($this->print_html_table($debug_vars));
		
		return $result;
		
	}// END - train
	
	//Tests a input against a input table
	//usage : bool test(array input_array, array train_result)
	//Each line of input_array is an input row and each column of each row is an input value
	//Ex: A test of a subject containing 4 columns
	//input_array = array(
	//		"x1" => 1, 
	//		"x2" => -1, 
	//		"x3" => 1, 
	//		"x4" => -1, ...
	//)
	//The second parameter "train_result" is the return from the function "train()"
	//return : boolean
	function test_class($input_array, $train_result) {
		
		//Sweeps input columns
		$result = 0;
		$i = 1;
		foreach($input_array as $column_value) {
			//Calculates teste Y
			$result += $train_result["w".$i] * $column_value;
			$i++;
		}
		
		//Checks in each class the test fits
		return ($result > 0) ? true : false;
		
	}// END - test_class
	
	// Returns the html code of a html table
	// base on a hash array
	function print_html_table($array) {
		$html = "";
		$inner_html = "";
		$table_header_composed = false;
		$table_header = array();
		
		// Builds table contents
		foreach ($array as $array_item) {
			$inner_html .= "<tr>\n";
			foreach ($array_item as $array_col_label => $array_col) {		
				$inner_html .= "<td>\n";
				$inner_html .= $array_col;
				$inner_html .= "</td>\n";
				
				if ($table_header_composed == false) {
					$table_header[] = $array_col_label;
				}
			}
			$table_header_composed = true;
			$inner_html .= "</tr>\n";
		}
		
		// Builds full table
		$html = "<table border=1>\n";
		$html .= "<tr>\n";
		foreach ($table_header as $table_header_item) {		
			$html .= "<td>\n";
			$html .= "<b>" . $table_header_item . "</b>";
			$html .= "</td>\n";
		}
		$html .= "</tr>\n";
				
		$html .= $inner_html . "</table>";
		
		return $html;
		
	}// END - print_html_table
	
	//Debug function
	function debug($message) {
		
		if ($this->debug == true) {
			echo "<b>DEBUG:</b> $message";
		}
		
	}// END - debug
	
}// END - class
?>