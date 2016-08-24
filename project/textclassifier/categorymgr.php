<?php

include_once 'category.php';

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

class categoryMgr
{
	var $listCategory = array();
	var $jumCategory;
	
	function categoryMgr()
	{
		$this->jumCategory = 0;
	}
	
	function addCategory($catname)
	{
		$cat = new category($catname);
		if (!isset($this->listCategory[$catname]))
		{
			$this->listCategory[$catname] = $cat;
			$this->jumCategory++;
		}
		else
		{
			die("nama kategori harus unik antara yang lainnya");
		}
	}
	
	function getAllJumlah()
	{
		$jum = 0;
		foreach ($this->listCategory as $obj)
		{
			$jum += $obj->jumWordInCategory;
		}
		return $jum;
	}
	
	function getCatProb($catname)
	{
		return $this->listCategory[$catname]->jumWordInCategory / $this->getAllJumlah();
	}
}

?>