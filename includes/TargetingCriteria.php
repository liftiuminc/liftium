<?php

class TargetingCriteria {

	public static function getCriteriaForTag($tag_id){

		static $dbr, $sth;
		if (empty($sth)){
                	$dbr = Framework::getDB("slave");
			$sql = "SELECT id, key_value, key_name FROM tag_targets WHERE tag_id = ?";
			$sth = $dbr->prepare($sql);
		}

		$sth->execute(array($tag_id));
		$out = array();
		while($row = $sth->fetch(PDO::FETCH_ASSOC)){
			$out[$row['key_name']] = self::splitValueString($row['key_value']);
		}
		return $out;	
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

