<?php


//require_once ("/Document_Hash.php");
//require_once (BASE_PATH . "/lib/Model.php");

/**
* @class Itemhashes_Model
* Model for news duplicates detection
*
* Usage:
* 1. Check if the news item already exists with isDup()
* 2. If it doesn't exist insert it to the database
* 3. Get id of inserted item with mysql_insert_id()
* 4. Tell this id to the instance of DupNewsModel with setDocId
* 5. Save the news item hashes with saveHashes()
*
* Notes:
* You need only text of the item for this class (without title, date etc).
* The text should be in UTF-8.
*
* @author Stanislav Perederiy
*/
class Itemhashes_Model
{
    protected $_table = "items_hashes_summary";
    protected $_table2 = "items_hashes";
	/// Instance of hashing class
	protected $_hash = null;
	/// Document id for saving hashes in database
	public $_docId = null;
	public $_orderId = null;

	public function __construct($text = null, $docId = null, $orderId = null)
	{
        //parent::__construct();
        if ( !empty($text) ) {
			$this->_hash = new Document_Hash($text);
        }else return false;
        

		$this->_docId = $docId;
        $this->_orderId = $orderId;
        //$this->initDB();
		//$this->_table2 = COUNTRY_DB_TABLE_PREFIX . $this->_table2;
	}

	public function setDocId ($docId)
    {
		$this->_docId = $docId;
		return $this;
	}

	public function isDup()
	{
        global $db;
        $idd = ''; $ids = array();
		if (!$this->_isHashValid()) {
			//throw new Exception("Error: It seems that something went wrong during hashing!", 500);
			return 0;
		}

		$conditions = "";
		if ( $this->_docId != null ) {
			$conditions = "doc_id != " . $this->_docId;
		}
		else {
			$conditions = "1";
		}

        $conditions.= " AND order_id=" . $this->_orderId;

		//$result = $this->_db->query("SELECT doc_id FROM {$this->_table} WHERE $conditions AND full_hash='{$this->_hash->docMD5}' AND length={$this->_hash->length}");

        $result = $db->query("SELECT doc_id FROM {$this->_table} WHERE $conditions AND full_hash='{$this->_hash->docMD5}' AND length={$this->_hash->length}");

        //echo "SELECT doc_id FROM {$this->_table} WHERE $conditions AND full_hash='{$this->_hash->docMD5}' AND length={$this->_hash->length} <br>";
        //echo "SELECT doc_id FROM {$this->_table} WHERE $conditions AND full_hash='{$this->_hash->docMD5}' AND length={$this->_hash->length}\n";
        //echo "\n".$this->_hash->docMD5."\n";

        //$id = mysql_fetch_array($result);
        //print_r($id);
        //if ( $id = $result->fetchColumn() ) {


        //TODO: тут косяк стопудово надо проверить ++ проверял

        while (list ($id) = mysql_fetch_array($result)) {
            //echo "Doc is equal to $id\n";
            $idd = $id;
            $ids[] = $id;
        }
        // return $id;
        // die();
        //}
        //var_dump($idd);
        //die();

        //echo "<span style='background: #adff2f;'> idd=".$idd." AND ids="; print_r($ids);echo "</span>";


        if (!is_object($this->_hash)) return false;
		$crc32 = $this->_hash->getCrc32array();
		$hashClause = "0";
		foreach ($crc32 as $token_hash) {
			$hashClause .= " OR word_hash=$token_hash";
		}

		//$result = $this->_db->query("SELECT doc_id, COUNT(id) as inters FROM {$this->_table2} WHERE $conditions AND ($hashClause) GROUP BY doc_id HAVING inters>1");
		$result = $db->query("SELECT doc_id, COUNT(id) as inters FROM {$this->_table2} WHERE $conditions AND ($hashClause) GROUP BY doc_id HAVING inters>1");
		//echo "SELECT doc_id, COUNT(id) as inters FROM {$this->_table2} WHERE $conditions AND ($hashClause) GROUP BY doc_id HAVING inters>1  \n";

		//while (list($id, $intersecs) = $result->fetch()) {
		//while (list($id, $intersecs) = $db->fetch($result)) {
		while (list($id, $intersecs) = mysql_fetch_array($result)) {
			$length='';

            $result2 = $db->query("SELECT length FROM {$this->_table} WHERE doc_id=$id");
            $length = $db->fetch($result2);
            //var_dump($length);
            $length = $length['length'];

            //$length = min($length, $this->_hash->length);
            $length = max($length, $this->_hash->length);
            $similarity = ($intersecs/$length)*100;  // Similarity between 2 docs in percents


            //echo "id=$id inters=$intersecs sim=$similarity\n";
            //echo "Detected $intersecs of $length intersections with $id ($similarity %)\n";
			if ($similarity > 75) {
              /*  echo "<h3>id = $this->_docId => {$this->_hash->_doc_content} </h3>";
                echo "<h3>length = $length</h3>";
                echo "<h3>hash length = {$this->_hash->length}</h3>";
                echo "<h3>intersecs = $intersecs</h3>";
                echo "<h2>finded id = $id => $similarity% </h2><hr>";*/
				$ids[]=$id;
			}

        }

        //echo "<span style='background: red'>ids=";print_r($ids);echo "</span>";

        if ($idd!='' || count($ids)) {
            //echo "<span style='background: #adff2f;'>"; print_r($ids);echo "</span>";
            return $ids;
        }

        //if (count($ids)) {echo "<span style='background: #adff2f'>$ids</span>"; return $ids; }

		return 0;
	}

	public function findSimilar()
	{
		$crc32 = $this->_hash->getCrc32array();
		$hashClause = "0";
		foreach ($crc32 as $token_hash) {
			$hashClause .= " OR word_hash=$token_hash";
		}

		if ( isset($this->_docId) ) {
			$doc_id_clause = "doc_id != " . $this->_docId;
		}
		else {
			$doc_id_clause = " 1 ";
		}

		//echo "SELECT doc_id, COUNT(id) as inters FROM {$this->_table2} WHERE $doc_id_clause AND ($hashClause) GROUP BY doc_id HAVING inters>1";
		$result = $this->_db->query("SELECT doc_id, COUNT(id) as inters FROM {$this->_table2} WHERE $doc_id_clause AND ($hashClause) GROUP BY doc_id HAVING inters>1");
		while (list($id, $intersects) = $result->fetch()) {
			$result2 = $this->_db->query("SELECT length FROM {$this->_table} WHERE doc_id=$id");
			$length = $result2->fetchColumn();
			$length = max($length, $this->_hash->length);
			$similarity = ($intersects/$length)*100;  // Similarity between 2 docs in percents
			if ($similarity > 75) {
				echo "Detected $intersects of $length intersections with $id ($similarity %)<br>\n";
			}

		}

	}

	public function save()
	{
        global $db;
		// Some checks:

		if ($this->_docId == null) {
			// throw new Exception("Error: _docId should be defined in order to save!", 500);
			return 0;
		}
		// Check if we have more or less correct results from hash class instace
		if (!$this->_isHashValid()) {
			//throw new Exception("Error: It seems that something went wrong during hashing! $this->_docId", 500);
			return 0;
		}
		// We have to check if we have this hashes of this document already
		//$result = $this->_db->query("SELECT doc_id FROM {$this->_table} WHERE doc_id={$this->_docId}");
		$result = $db->query("SELECT doc_id FROM {$this->_table} WHERE doc_id={$this->_docId}");
		//$result2 = $this->_db->query("SELECT doc_id FROM {$this->_table2} WHERE doc_id={$this->_docId} LIMIT 0,1");
		$result2 = $db->query("SELECT doc_id FROM {$this->_table2} WHERE doc_id={$this->_docId} LIMIT 0,1");
		$res1 = $db->fetch($result);
		$res2 = $db->fetch($result2);
        if ( $res1['doc_id']!='' || $res2['doc_id']!='' ) {
			// And delete them if we do
			$this->delete();
		}


		// Saving process itself:
		//$this->_db->query("INSERT INTO {$this->_table} SET doc_id={$this->_docId},
		$db->query("INSERT INTO {$this->_table} SET doc_id={$this->_docId},
																full_hash='{$this->_hash->docMD5}',
																length='{$this->_hash->length}',
																order_id = '{$this->_orderId}'");
        
		$crc32 = $this->_hash->getCrc32array();
		foreach ($crc32 as $word_hash) {
			//$this->_db->query("INSERT INTO {$this->_table2} SET doc_id={$this->_docId}, word_hash={$word_hash}");
			$db->query("INSERT INTO {$this->_table2} SET doc_id={$this->_docId}, word_hash={$word_hash}");
		}

	}

	public function delete()
	{
        global $db;
		//$this->_db->query("DELETE FROM {$this->_table} WHERE doc_id=".$this->_docId);
		$db->query("DELETE FROM {$this->_table} WHERE doc_id=".$this->_docId);
		//$this->_db->query("DELETE FROM {$this->_table2} WHERE doc_id=".$this->_docId);
		$db->query("DELETE FROM {$this->_table2} WHERE doc_id=".$this->_docId);
	}

	protected function _isHashValid()
	{
        //echo $this->_hash->length." - hash length - $this->_docId \n";
        if ($this->_hash->length < 1 && $this->_hash->docMD5 != '') {
			return 0;
		}
		return 1;
	}

}

/*

Useful queries:
SELECT doc_id, full_hash, COUNT(*) as qty FROM `terdo_items_hashes_summary` GROUP BY full_hash HAVING qty>1

SELECT doc_id, word_hash, COUNT(*) as qty FROM `terdo_items_hashes` GROUP BY word_hash HAVING qty>1

Tables sctrucure:

CREATE TABLE `items_hashes_summary` (
  `doc_id` int(11) NOT NULL,
  `full_hash` char(32) collate utf8_unicode_ci NOT NULL,
  `length` smallint(11) NOT NULL,
  PRIMARY KEY  (`doc_id`)
)


CREATE TABLE `items_hashes` (
  `id` int(11) NOT NULL auto_increment,
  `doc_id` int(11) NOT NULL,
  `word_hash` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
)


 */
?>
