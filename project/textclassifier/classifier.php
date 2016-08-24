<?php

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

class classifier
{
	var $catmgr;
	
	function classifier()
	{
		$txt_catmgr = file_get_contents('wordprob.srl') or 
						die("PHPTextClassifier error : file wordprob.srl tidak ada !");
		$this->catmgr = unserialize($txt_catmgr);
	}
	
	function classifyFile($path)
	{	
		$datafile = file_get_contents ($path);
		return $this->classifyText($datafile);
	}
	
	function classifyText($text)
	{
		$arr_nama = array();
		$arr_nilai = array();
		$arr_cat = $this->catmgr->listCategory;
		$i = 0;
		foreach ($arr_cat as $obj_cat)
		{
			$arr_nilai[$i] = $this->computeProb($text, $obj_cat->namaCat);
			$arr_nama[$i] = $obj_cat->namaCat;
			$i++;
		}
		
		$i = 1;
		$inmax = 0;
		while ($i < strlen($arr_nilai))
		{
			if ($arr_nilai[$inmax] < $arr_nilai[$i])
			{
				$inmax = $i;
			}
			$i++;
		}
		
		return $arr_nama[$inmax];
	}
	
	function computeProb($text, $catname)
	{
		$arr_data = explode(' ',$text);
		if (!isset($this->catmgr->listCategory[$catname])) 
		{
			die ("PHPTextClassifier error : Nama kategori '$catname' tidak ada !");
		}
		$wordprb = $this->catmgr->listCategory[$catname]->wordProb->words;
		$hasil = 0; /*dalama logaritma*/
		
		foreach ($arr_data as $txt)
		{
			if (isset($wordprb[strtolower($txt)]))
			{
				$hasil += log($wordprb[strtolower($txt)]);
				//echo $wordprb[strtolower($txt)].' ';
			}
			else
			{
				$hasil += log(1 / ($this->catmgr->listCategory[$catname]->jumWordInCategory + $this->catmgr->listCategory[$catname]->jumVocab));
				//echo $this->catmgr->listCategory[$catname]->jumVocab;
				//echo (1 / ($this->catmgr->listCategory[$catname]->jumWordInCategory + $this->catmgr->listCategory[$catname]->jumVocab)); 
			}
		}
		
		$hasil += log($this->catmgr->getCatProb($catname));
		
		return $hasil;
	}
}


?>