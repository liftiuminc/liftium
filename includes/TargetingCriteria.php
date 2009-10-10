<?php

class TargetingCriteria {

	public function getKeys(){
                $dbr = Framework::getDB("slave");
		$out = array();
		$sql = "SELECT * FROM target_key ORDER BY target_keyname";
		$sth = $dbr->prepare($sql);
		$sth->execute();
		while($row = $sth->fetch(PDO::FETCH_ASSOC)){
			$out[$row['target_key_id']] = $row['target_keyname'];
		}
		return $out;	
	}

	public function getKeyValues($keyname = null){
                $dbr = Framework::getDB("slave");

		$values = array();
		$sql = "SELECT target_value.*, target_keyname FROM target_value
			INNER JOIN target_key ON
				target_key.target_key_id = target_value.target_key_id
			WHERE 1 = 1";
		if (!empty($keyname)){
			$sql .= " AND target_keyname = ?";
			$values[] = $keyname;
		}

		$sth = $dbr->prepare($sql);
		$sth->execute($values);
		$out = array();
		while($row = $sth->fetch(PDO::FETCH_ASSOC)){
			$out[$row['target_value_id']] = $row;
		}
		return $out;	
	}

	public function getThinKeyValues($keyname){
		$kvs = $this->getKeyValues($keyname);
		$out = array();
		foreach($kvs as $kv){
			$out[] = $kv['target_keyvalue'];
		}
		return $out;
	}
	
	public static function getCriteriaForTag($tag_id){
                $dbr = Framework::getDB("slave");

		$sql = "SELECT target_value.*, target_keyname FROM target_value
			INNER JOIN target_key ON
				target_key.target_key_id = target_value.target_key_id
			INNER JOIN target_tag_linking ON
				target_tag_linking.target_value_id = target_value.target_value_id AND
				target_tag_linking.tag_id = ?";

		$sth = $dbr->prepare($sql);
		$sth->execute(array($tag_id));
		$out = array();
		while($row = $sth->fetch(PDO::FETCH_ASSOC)){
			$out[$row['target_value_id']] = $row;
		}
		return $out;	
	}

	static public function setCriteriaForTag($tag_id, $target_value_ids = null){
		$dbw = Framework::getDB("master");

		if (empty($tag_id)){
			return false;
		}

		$dbw->beginTransaction();
		$sth = $dbw->prepare("DELETE FROM target_tag_linking WHERE tag_id = ?");
		$sth->execute(array($tag_id));

		$ret = 0;
                if (!empty($target_value_ids)){
                        $sql = "INSERT INTO target_tag_linking VALUES (?, ?)";
						$sth = $dbw->prepare($sql);
                        foreach($target_value_ids as $tv_id){
                                $sth->execute(array($tag_id, $tv_id));
                                $ret++;
                        }
                }


		$dbw->commit();

		return $ret;

	}


	public static function getThinCriteriaForTag($tag_id){
		$rawCriteria = TargetingCriteria::getCriteriaForTag($tag_id);
		$out = array();
		if (empty($rawCriteria)){
			return $out;
		}

		foreach($rawCriteria as $rc){
			$out[$rc['target_keyname']][] = $rc['target_keyvalue'];
		}
		return $out;
				
	}

	public static function getAsidsFromString($keyname, $string){
		$dbw = Framework::getDB("master");

		$out = array();
		if (empty($keyname) || empty($string)){
			return $out;
		}

		$pieces = self::splitValueString($string);
		if (empty($pieces)){
			return $out;
		}


		$keyid = self::getKeyValueId($keyname);
		if (empty($keyid)){
			return false;
		}

		foreach($pieces as $piece){
			// Check if it's already there
			$tvid = self::getTargetValueId($keyid, $piece);
			if (empty($tvid)){
				// Otherwise insert it
				$sql = "INSERT INTO target_value VALUES(NULL, ?, ?)";
				$sth = $dbw->prepare($sql);
				$sth->execute(array($keyid, $piece));
				$tvid  = $dbw->lastInsertId();
			}
			$out[] = $tvid;
		}

		return $out;
	}

	static public function getKeyValueId($keyname){
		$dbr = Framework::getDB("slave");
		$sql = "SELECT target_key_id FROM target_key WHERE target_keyname = ?";
		$sth = $dbr->prepare($sql);
		$sth->execute(array($keyname));
		while($row = $sth->fetch(PDO::FETCH_ASSOC)){
			return $row['target_key_id'];
		}
		return false;
	}


	static public function getTargetValueId($keyid, $keyvalue){
		$dbr = Framework::getDB("slave");
		$sql = "SELECT target_value_id FROM target_value WHERE target_key_id = ?
			AND target_keyvalue = ?";
		$sth = $dbr->prepare($sql);
		$sth->execute(array(intval($keyid), $keyvalue));
		while($row = $sth->fetch(PDO::FETCH_ASSOC)){
			return $row['target_value_id'];
		}
		return false;
	}

	/* Values can be comma or semi colon delimited, and we trim white space */
	static public function splitValueString($in){
		$out = array();
		foreach (preg_split('/[,;]/', $in) as $piece){
			if (trim($piece) != ''){
				$out[] = trim($piece);
			}
		}
		return array_unique($out);
	}

}

