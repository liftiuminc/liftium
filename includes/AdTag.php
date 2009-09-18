<?php

class AdTag {

	public $tag_id;
	public $tag_name, $notes, $enabled, $network_id, $estimated_cpm, $threshold, $tier,
		$value, $tag, $guaranteed_fill, $sample_rate, $freq_cap, $rej_cap, $rej_time,
		$auto_update_ecpm, $reported_ecpm, $reported_date;
	public $as_ids = null, $options = null;


	public function __construct($tag_id=null){
		if (!is_null($tag_id)){
			$this->loadFromId($tag_id);
		}
	}

	public function loadFromId($tag_id){
		$dbr = Framework::getDB("slave");
		$sql = "SELECT * FROM tag WHERE id=" . $dbr->quote($tag_id);
		foreach($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			foreach($row as $column => $data){
				$this->$column = $data;
			}
			$this->value = round($row['value'], 2);
		}


		$sql = "SELECT option_name, option_value FROM tag_option WHERE tag_id=" . $dbr->quote($tag_id);
		$this->options = null;
		foreach($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			$this->options[$row['option_name']] = $row['option_value'];
		}
	}

	public function getCurrentSizes($tag_id = null){
		if (is_null($tag_id)){
			$tag_id = $this->tag_id;
		}

		$dbr = Framework::getDB("slave");
		$sql = "SELECT DISTINCT size FROM ad_slot
			INNER JOIN tag_slot_linking ON ad_slot.as_id = tag_slot_linking.as_id
				AND tag_slot_linking.tag_id=" . $dbr->quote($tag_id);
		$out = array();
		foreach ($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			$out[] = $row['size'];
		}

		return $out;
	}

	public function getSizes(){
		$excludedSizes = array('200x75', '125x125');

		$out = array();

		$dbr = Framework::getDB("slave");
		$sql = "SELECT size FROM adformats";
		foreach ($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			if (!in_array($row['size'], $excludedSizes)){
				$out[] = $row['size'];
			}
		}

		return $out;
	}

	public static function getSlots(){
		$excludedSizes = array('200x75', '125x125');

		$out = array();

		$dbr = Framework::getDB("slave");
		$sql = "SELECT slot, size FROM ad_slot WHERE default_enabled = 1
			AND size NOT IN ('" . implode("','", $excludedSizes) . "')
			AND skin='monaco' ORDER BY size, slot";
		foreach ($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			$out[$row['slot']] = $row['size'];
		}

		return $out;
	}




	private function saveSlots(){
		if (is_null($this->as_ids)){ // Note that if you want to clear slots, set $this->as_ids to an empty array
			return false;
		}

		if(empty($this->tag_id)){
			trigger_error("tag_id must be set for saveSlots()", E_USER_WARNING);
			return false;
		}

		$dbw = Framework::getDB("master");
		$dbw->exec("BEGIN");
		$dbw->exec("DELETE FROM tag_slot_linking WHERE tag_id =" . $dbw->quote($this->tag_id));

		if (!empty($this->as_ids)){
			$sql = "INSERT INTO tag_slot_linking VALUES ";
			foreach($this->as_ids as $as_id){
				$sql.="($this->tag_id, $as_id),";
			}
			$sql = preg_replace('/,$/', ';', $sql);
			$ret = $dbw->exec($sql);
		}

		$dbw->exec("COMMIT");
		return $ret;
	}


	private function saveOptions(){
		if (is_null($this->options)){ // Note that if you want to clear options, set $this->options to an empty array
			return false;
		}

		if(empty($this->tag_id)){
			trigger_error("tag_id must be set for saveOptions()", E_USER_WARNING);
			return false;
		}

		$dbw = Framework::getDB("master");
		$dbw->exec("BEGIN");
		$dbw->exec("DELETE FROM tag_option WHERE tag_id = " . $dbw->quote($this->tag_id));

		if (!empty($this->options)){
			$sql = "INSERT INTO tag_option VALUES ";
			$values = 0;
			foreach($this->options as $on => $ov){
				if ($ov == ''){
					continue; // Don't store empty values
				}
				else {
					$values++;
				}
				$sql .= "($this->tag_id, " . $dbw->quote($on) . "," .$dbw->quote($ov) . "),";
			}

			$sql = preg_replace('/,$/', ';', $sql);
			if($values) {
				$ret = $dbw->exec($sql);
			}
		}

		$dbw->exec("COMMIT");
		return $ret;
	}

	static public function searchTags($criteria=array(), $objects = true){
		$dbr = Framework::getDB("slave");
		/* The idea behind weighted_random_value is that we want to sort items
		 * within the same tier randomly (to take advantage of cream skimming)
		 * But we also want to favor the higher paying ads
		 */
		$sql = "SELECT SQL_SMALL_RESULT /* Tell mysql to use in memory temp tables */
			id AS tag_id,
			(rand() * (0.1 * value)) AS weighted_random_value
			FROM tags WHERE 1=1";
		if (!empty($criteria['name_search'])){
			$search = '%' . $criteria['name_search'] . '%';
			$sql .= "\n\tAND tag_name like " . $dbr->quote($search) . " ";
		}

		if (!empty($criteria['enabled'])){
			$sql .= "\n\tAND enabled = " . $dbr->quote($criteria['enabled']) . " ";
			$sql .= "\n\tAND network_id in (SELECT network_id from networks where enabled = 1)";
		}

		if (!empty($criteria['network_id'])){
			$sql .= "\n\tAND network_id = " . $dbr->quote($criteria['network_id']) . " ";
		}

		if (!empty($criteria['pubid'])){
			$sql .= "\n\tAND publisher_id = " . $dbr->quote($criteria['pubid']) . " ";
		}

		if (!empty($criteria['size'])){
			$sql .= "\n\tAND size = " . $dbr->quote($criteria['size']) . " ";
		}

		if (!empty($criteria['auto_update_ecpm'])){
			$sql .= "\n\tAND auto_update_ecpm = " . $dbr->quote($criteria['auto_update_ecpm']) . " ";
		}


		// Get a cup of coffee first
		if (!empty($criteria['target_country'])){
			$sql .= "\n\tAND (
				tag_id NOT IN (
				  -- No targeting for country
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id = 1)
				OR tag_id IN (
				  -- This specific country is targeted
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id = 1 AND target_keyvalue = " . $dbr->quote($criteria['target_country']) . "
				 )
			       )";

		}

		if (!empty($criteria['target_browser'])){
			$sql .= "\n\tAND (
				tag_id NOT IN (
				  -- No targeting for browser
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id = 7)
				OR tag_id IN (
				  -- This specific browser is targeted
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id = 7 AND target_keyvalue = " . $dbr->quote($criteria['target_browser']) . "
				  )
				)";
		}

		if (!empty($criteria['target_hub'])){
			$sql.= "\n\tAND (
				tag_id NOT IN (
				  -- No targeting for hub
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id = 2)
				OR tag_id IN (
				  -- This specific hub is targeted
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id = 2 AND target_keyvalue = " . $dbr->quote($criteria['target_hub']) . "
				 )
			       )";
		}

		if (!empty($criteria['wgDBname'])){
			$sql.= "\n\tAND (
				tag_id NOT IN (
				  -- No targeting for wgDBname
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id = 4)
				OR tag_id IN (
				  -- This specific wgDBname is targeted
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id = 4 AND target_keyvalue = " . $dbr->quote($criteria['wgDBname']) . "
				 )
			       )";
		}

		// Exclude site specific tags
		if (!empty($criteria['exclude_site_specific'])){
			$sql.= "\n\tAND tag_id NOT IN (
				  SELECT tag_id FROM target_tag_linking
					INNER JOIN target_value
					ON target_tag_linking.target_value_id = target_value.target_value_id
				  WHERE target_key_id IN (3,4))";
		}

		if (!empty($criteria['slotname'])){
			$sql.= "\n\tAND tag_id IN (
				  SELECT DISTINCT tag_id FROM tag_slot_linking
					INNER JOIN ad_slot ON tag_slot_linking.as_id = ad_slot.as_id
					 AND ad_slot.slot = " . $dbr->quote($criteria['slotname']) .  ")";
		}

		switch (@$criteria['sort']){
			case 'name': $sql.= "\n\tORDER BY tag_name"; break;
			case 'value,rand': $sql.= "\n\tORDER BY value DESC, rand()"; break;
			case 'chain': $sql.= "\n\tORDER BY tier DESC, weighted_random_value DESC"; break;
			case 'tier': $sql.= "\n\tORDER BY tier DESC"; break;
		}

		if (intval(@$criteria['limit']) > 0 ){
			$sql .= " LIMIT " . $criteria['limit'];
		}
		if (intval(@$criteria['offset']) > 0 ){
			$sql .= " OFFSET " . $criteria['offset'];
		}

		if (!empty($_GET['debug'])){
			echo "<xmp>" . $sql . "</xmp>";
		}
		$out = array();
		foreach($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			if ($objects){
				$out[] = new AdTag($row['tag_id']);
			} else {
				$out[] = $row['tag_id'];
			}
		}

		return $out;
	}


	public function getFillStats($criteria, $tag_id = null){
		if (is_null($tag_id)){
			$tag_id = $this->tag_id;
		}

		$dbr = Framework::getDB("slave");

		$sql = "SELECT SUM(attempts) AS attempts, SUM(rejects) AS rejects,
			SUM(loads) AS loads,
			SUM(loads)/SUM(attempts) AS fill_rate
			FROM fills_minute WHERE
			tag_id = " . $dbr->quote($tag_id);

		if (!empty($criteria['since'])){
			$sql.= " AND minute >= " . $dbr->quote(date('Y-m-d H:i:00', $criteria['since']));
		} else if (!empty($criteria['minute'])){
			$sql.= " AND minute = " . $dbr->quote(date('Y-m-d H:i:00', $criteria['minute']));
		}

		$placeholder = array('attempts'=>0, 'rejects'=>0, 'loads'=>0, 'fill_rate'=>0);
		foreach($dbr->query($sql, PDO::FETCH_ASSOC) as $row){
			$out = $row;
		}
		if (empty($out['attempts'])){
			return $placeholder;
		} else {
			return $out;
		}

	}

        /* Size is stored as $widthx$size character column. Split here.
         * You may be asking, why not just store it as separate values to be begin with?
         * Because size is not always height/width. Possible values for size include:
         * 728x60
         * 300x250,300x600
         * 728x*
         *
         * Do the best you can to return a height/width
         */
        public static function getHeightWidthFromSize($size){
                if (preg_match('/^([0-9]{2,4})x([0-9]{2,4})/', $size, $matches)){
                        return array('width'=>$matches[1], 'height'=>$matches[2]);
                } else if (preg_match('/^([0-9]{2,4})x\*/', $size, $matches)){
                        return array('width'=>$matches[1], 'height'=>'*');
                } else {
                        return false;
                }
        }
}

