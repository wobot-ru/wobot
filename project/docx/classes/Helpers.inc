<?php
 function repairZipOpenOffice($file) { $fileRepaired = new ZipArchive(); $fileRepaired->open($file); $this->_docxContent = array(); for ($i = 0; $i < $fileRepaired->numFiles; $i++) { $this->_docxContent[$fileRepaired->getNameIndex($i)] = $fileRepaired->getFromName($fileRepaired->getNameIndex($i)); } foreach ($this->_docxContent as $key => $value) { $fileRepaired->addFromString($key, $value); } $fileRepaired->close(); }