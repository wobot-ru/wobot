<?php

include_once 'wordcount.php';
include_once 'categorymgr.php';

/*=======================================================================*/
/* PHPTextClassifier adalah sebuah modul yang digunakan untuk mengklasi- *
 * fikasi sebuah text menjadi sebuah kategori tertentu berdasarkan kate- *
 * gori yang diberikan oleh pengguna. Metode yang digunakan adalah dengan*
 * metode Naive Bayesian Classifier (NBC).						      *
 *************************************************************************
 * PHPTextClassifier adalah Open SOurce, jadi Anda bebas untuk           *
 * meng-oprek kode ini. Tetapi tidak untuk dikomer-                      *
 * silkan.                                                               *
 ************************G-P-L********************************************
 *                                                                       *
 *  This program is free software: you can redistribute it and/or modify *
 *  it under the terms of the GNU General Public License as published by *
 *  the Free Software Foundation, either version 3 of the License, or    *
 *  (at your option) any later version.                                  *
 *                                                                       *  
 *  This program is distributed in the hope that it will be useful,      *
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of       *
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        *
 *  GNU General Public License for more details.                         *
 *                                                                       *
 *  You should have received a copy of the GNU General Public License    *
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.*
 *                                                                       *
 *************************************************************************
 * Copyright 2008 Alfan Farizki Wicaksono [ITB]                          *
 *************************************************************************
 *************************************************************************/

class trainer
{
	var $excludedWords = array ( 'dan',
								 'kepada',
								 'sebuah',
								 'untuk',
								 'dengan',
								 'ketika',
								 'pada',
								 'di',
								 'pada',
								 'walaupun',
								 'kalau',
								 'jika',
								 'adalah',
								 'maka'
							);
	var $ctgrmgr;
	var $arr_wc = array();
	var $debug = false;
	
	function trainer()
	{
		$this->ctgrmgr = new categoryMgr();
	}
	
	function makeCategory($ar_kat)
	{
		/*isi word count / vocab*/
		foreach($ar_kat as $val)
		{
			$this->trainNewCategory($val, $val);
		}
		
		/*isi word probability*/
		foreach($ar_kat as $val) //untuk setiap kategori
		{
			foreach ($this->arr_wc[$val]->vocab as $key => $row)
			{
				$prob = (1 + $row) / ($this->ctgrmgr->listCategory[$val]->jumWordInCategory + $this->arr_wc[$val]->jumVocab);
				//echo "$row ";
				$this->ctgrmgr->listCategory[$val]->addWordProb($key, $prob);
			}
			if ($this->debug)
			{
				echo "<br/>";
				echo "jum in category : "; echo $this->ctgrmgr->listCategory[$val]->jumWordInCategory; echo "<br/>";
				echo "jum vocab : "; echo $this->arr_wc[$val]->jumVocab; echo "<br/>";
				echo "get cat prob $val : "; echo $this->ctgrmgr->getCatProb($val); echo "<br/>";
			}
		}
		$this->serializeCat();
	}
	
	function trainNewCategory($catname, $dir)
	{
		//tambah kategori
		$this->ctgrmgr->addCategory($catname);
		
		//train kategori
		if ($d = opendir('cat/'.$dir))
		{	
			$data = '';
			$jumfile = 0;
			while ($f1 = readdir($d))
			{
				$path = 'cat/'.$dir.'/'.$f1;
				$info = pathinfo($path);
				if (is_file($path) && ($info['extension'] == 'txt'))
				{
					//$str_include = $str_include."include_once '$path'; ";
					$data = file_get_contents($path).$data;
					$jumfile++;
					//echo $path;
				}
			}
			if (($jumfile == 0) || (strlen($data) == 0))
			{
				die ("PHPTextClassifier error : Tidak ada file trainer di folder 'cat/$dir' ");
			}
			$this->trainData($catname, $data);
			closedir($d);
		}
		else
		{
			die ("PHPTextClassifier error : Direktori 'cat/$dir' tidak ada !");
		}
	}
	
	function trainData($catname, $data)
	{
		/*Proes untuk menghitung frekuensi kata kata*/
		$wc = new wordcount();
		$jumkata = 0; /*jumlah kata di dalam data*/
		
		/*Mulai komputasi*/
		$words = preg_split('/\s+/',$data,-1,PREG_SPLIT_NO_EMPTY);
		foreach ($words as $word) 
		{
			$temp = '';
			$i = 0;
			while ($i < strlen($word))
			{
				$c = $word[$i];
				if (($c>='a' && $c<='z') || ($c>='A' && $c<='Z') || ($c>='0' && $c<='9'))
				{
					$temp .= $c;
				}
				$i++;
			}
			$word = $temp;
			if (preg_match('/^[a-zA-Z0-9]+$/',$word) && (! in_array($word, $this->excludedWords)))
			{
				//echo "$word <br/>";
				$wc->addWord($word);
				$jumkata++;
			}
		}
		/*masukan jumlah total kata pada satu kategori*/
		$this->ctgrmgr->listCategory[$catname]->incJumWord($jumkata);
		
		/*masukan jumlah vocab ke category */
		$this->ctgrmgr->listCategory[$catname]->jumVocab = $wc->jumVocab;
		
		/*masukan wordcount ke array*/
		$this->arr_wc[$catname] = $wc;
	}
	
	function serializeCat()
	{
		$ser = serialize($this->ctgrmgr);
		$fp = fopen('wordprob.srl', 'w');
		if ($fp === false)
		{
			die('PHPTextClassifier error : tidak bisa menulis serialisasi ke file wordprob.srl !');
		}
		else
		{
			$byte = fwrite($fp, $ser);
			fclose($fp);
		}
	}
}


?>