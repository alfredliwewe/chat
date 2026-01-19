<?php

function time_ago($time){
    $time = (int)trim($time);
	$labels = [
		['s', 60],
		['min', 3600],
		['h', 3600 * 24],
		['d', 3600 * 24 * 7],
		['w', 3600 * 24 * 7 * 4],
		['mon', 3600 * 24 * 7 * 30],
		['y', 3600 * 24 * 7 * 30 * 12]
	];

	$dif = time() - $time;

	$can = true;
	$label = null;
	$div = 1;

	if ($dif == 0) {
		return "now";
	}

	for ($i=0; $i < count($labels); $i++) { 
		if ($dif < $labels[$i][1]) {
			if($can){
				$can = false;
				$label = $labels[$i][0];

				if($i != 0){
					$div = $labels[$i-1][1];
				}
			}
		}
	}

	if ($label == null) {
		return "Unknown";
	}
	else{
		return floor($dif/$div).$label;
	}
}

function getCountries(){
	global $db;
	$countries = [];
	$sql = $db->query("SELECT * FROM countries");
	while ($row = $sql->fetch_assoc()) {
		$countries[$row['id']] = $row;
	}

	$countries[0] = ['id' => 0, 'name' => ""];

	return $countries;
}


function db_update($table, $cv, $where){
	global $db;

	$contentValues = [];
	foreach ($cv as $key => $value) {
		$value = $db->real_escape_string($value);
		array_push($contentValues, "`$key` = '$value'");
	}

	$whereClause = [];
	foreach ($where as $key => $value) {
		if(is_array($value)){
			$values = [];
			foreach ($value as $v) {
				array_push($values, "'".$db->real_escape_string($v)."'");
			}
			array_push($whereClause, "`$key` IN (".implode(",", $values).")");
		}
		else{
			if(count(explode(" ", trim($key))) > 1){
				$value = $db->real_escape_string($value);
				array_push($whereClause, "$key '$value' ");
			}
			else{
				$value = $db->real_escape_string($value);
				array_push($whereClause, "`$key` = '$value' ");
			}
		}
	}

	$db->query("UPDATE `$table` SET ".implode(", ", $contentValues)." WHERE ".implode(" AND ", $whereClause));
	return $db->affected_rows;
}

function getData($table, $array){
	global $db;

	$wheres = [];

	foreach ($array as $key => $value) {
		if(count(explode(" ", trim($key))) > 1){
			$value = $db->real_escape_string($value);
			array_push($wheres, "$key '$value' ");
		}
		else{
			$value = $db->real_escape_string($value);
			array_push($wheres, "`$key` = '$value' ");
		}
	}

	if (count($wheres) == 0) {
		return null;
	}

	return $db->query("SELECT * FROM `$table` WHERE ".implode(" AND ", $wheres)." LIMIT 1")->fetch_assoc();
}

function getAll($table, $ref=null, $extra=null){
	global $db;

	if ($ref == null) {
		$read = $db->query("SELECT * FROM `$table` ");
		$rows = [];
		while ($row = $read->fetch_assoc()) {
			array_push($rows, $row);
		}

		return $rows;
	}
	elseif (is_array($ref)) {
		$wheres = [];

		foreach ($ref as $key => $value) {
			if(is_array($value)){
				if(count($value) == 0) {
					$wheres[] = "`$key` = 0";
					continue;
				};
				$values = [];
				foreach ($value as $v) {
					array_push($values, "'".$db->real_escape_string($v)."'");
				}
				array_push($wheres, "`$key` IN (".implode(",", $values).")");
			}
			else{
				$value = $db->real_escape_string($value);
				if(count(explode(" ", trim($key))) > 1){
					array_push($wheres, "$key '$value' ");
				}
				else{
					array_push($wheres, "`$key` = '$value' ");
				}
			}
		}

		$read = $db->query("SELECT * FROM `$table` WHERE ".implode(" AND ", $wheres));
		$rows = [];
		while ($row = $read->fetch_assoc()) {
			array_push($rows, $row);
		}

		return $rows;
	}
	else{
		if ($extra != null) {
			$wheres = [];

			foreach ($extra as $key => $value) {
				if(is_array($value)){
					if(count($value) == 0) return [];
					$values = [];
					foreach ($value as $v) {
						array_push($values, "'".$db->real_escape_string($v)."'");
					}
					array_push($wheres, "`$key` IN (".implode(",", $values).")");
				}
				else{
					if(count(explode(" ", trim($key))) > 1){
						array_push($wheres, "$key '$value' ");
					}
					else{
						array_push($wheres, "`$key` = '$value' ");
					}
				}
			}

			$read = $db->query("SELECT * FROM `$table` WHERE ".implode(" AND ", $wheres));
		}
		else{
			$read = $db->query("SELECT * FROM `$table` ");
		}

		$rows = [];
		while ($row = $read->fetch_assoc()) {
			$rows[$row[$ref]] = $row;
		}

		return $rows;
	}
}

function db_default($table){
	global $db;

	$read = $db->query("PRAGMA table_info(`$table`)");
	$row = [];
	while ($r = $read->fetch_assoc()) {
		$row[$r['name']] = $r['type'] == "INTEGER" ? 0 :"";
	}

	return $row;
}

function db_query($query, $column=null){
	global $db;
	$result = $db->query($query);
	$rows = [];
	while($row = $result->fetch_assoc()){
		if($column != null){
			$rows[$row[$column]] = $row;
		}
		else{
			array_push($rows, $row);
		}
	}
	return $rows;
}

function db_get_count($table, $col, $ref=null){
	global $db;

	$wheres = [];

	if ($ref != null) {
		foreach ($ref as $key => $value) {
			if(is_array($value)){
				if(count($value) == 0) {
					$wheres[] = "`$key` = 0";
					continue;
				};
				$values = [];
				foreach ($value as $v) {
					array_push($values, "'".$db->real_escape_string($v)."'");
				}
				array_push($wheres, "`$key` IN (".implode(",", $values).")");
			}
			else{
				$value = $db->real_escape_string($value);
				if(count(explode(" ", trim($key))) > 1){
					array_push($wheres, "$key '$value' ");
				}
				else{
					array_push($wheres, "`$key` = '$value' ");
				}
			}
		}
	}

	$wheres_str = count($wheres) > 0 ? " WHERE ".implode(" AND ", $wheres) : "";

	return (int)($db->query("SELECT COUNT(`$col`) AS count_all FROM `$table` $wheres_str")->fetch_assoc()['count_all']);
}

class Crypto{
	public static function uid($length)
	{
		$characters = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
		$str = "";
		for ($i=0; $i < $length; $i++) { 
			$str .= $characters[rand(0,count($characters)-1)];
		}
		return $str;
	}

	public static function letters_numbers($length)
	{
		$characters = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
		$str = "";
		for ($i=0; $i < $length; $i++) { 
			$str .= $characters[rand(0,count($characters)-1)];
		}
		return $str;
	}

	public static function letters($length)
	{
		$characters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
		$str = "";
		for ($i=0; $i < $length; $i++) { 
			$str .= $characters[rand(0,count($characters)-1)];
		}
		return $str;
	}
}

function guidv4($data = null) {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = $data ?? random_bytes(16);
    assert(strlen($data) == 16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
function db_insert($table, $array)
{
	global $db;

	$columns = [];
	$values = [];
	$read = $db->query("PRAGMA table_info(`$table`)");
	while ($row = $read->fetch_assoc()) {
		array_push($columns, "`{$row['name']}`");
		if ($row['pk'] == 1) {
			array_push($values, "NULL");
		}
		else{
			$value = isset($array[$row['name']]) ? $db->real_escape_string($array[$row['name']]) : "0";
			array_push($values, "'$value'");
		}
	}

	$sql = "INSERT INTO `$table` (".implode(",",$columns).") VALUES (".implode(",",$values).")";
	$db->query($sql);
	return $db->lastInsertRowID();
}

function getColumnNames($db, $table)
{
	$columns = [];
	//$rows = [];

	$read = $db->query("PRAGMA table_info(`$table`)");
	while ($row = $read->fetchArray(SQLITE3_ASSOC)) {
		//$columns = array_keys($row);
		array_push($columns, $row['name']);
	}

	return $columns;
}

function db_delete($table, $where){
	global $db;

	$whereClause = [];
	foreach ($where as $key => $value) {
		$value = $db->real_escape_string($value);
		array_push($whereClause, "`$key` = '$value'");
	}

	return $db->query("DELETE FROM `$table` WHERE ".implode(" AND ", $whereClause));
}

function db_read($table, $where, $ref=null){
	global $db;

	$whereClause = [];
	foreach ($where as $key => $value) {
		$value = $db->real_escape_string($value);
		array_push($whereClause, "`$key` = '$value'");
	}

	$res = $db->query("SELECT * FROM `$table` WHERE ".implode(" AND ", $whereClause));

	$rows = [];
	if ($ref == null) {
		while ($row = $res->fetch_assoc()) {
			array_push($rows, $row);
		}
	}
	else{
		while ($row = $res->fetch_assoc()) {
			$rows[$row[$ref]] = $row;
		}
	}

	return $rows;
}

function fileExtension($filename){
	$chars = explode(".", $filename);
	return strtolower($chars[count($chars)-1]);
}

function make_name($str){
	$characters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', ' '];
	$result = "";
	$str = strtolower($str);

	for ($i=0; $i < strlen($str); $i++) { 
		if (in_array($str[$i], $characters)) {
			$result = $result == "" ? $str[$i] : $result.$str[$i];
		}
	}

	return str_replace(" ", "_", $result);
}

$image_extensions = ["jpg","png","jpeg","gif","webp"];

//echo make_name("My name is Alfred Kang'oma");

function curl_post($url, array $post = NULL, array $options = array()) { 
	$defaults = array( 
		CURLOPT_POST => 1, 
		CURLOPT_HEADER => 0, 
		CURLOPT_URL => $url, 
		CURLOPT_FRESH_CONNECT => 1, 
		CURLOPT_RETURNTRANSFER => 1, 
		CURLOPT_FORBID_REUSE => 1, 
		CURLOPT_TIMEOUT => 4, 
		CURLOPT_POSTFIELDS => http_build_query($post) 
	); 

	$ch = curl_init(); 
	curl_setopt_array($ch, ($options + $defaults)); 
	if( ! $result = curl_exec($ch)) { 
		//trigger_error(curl_error($ch)); 
		return curl_error($ch);
	} 
	else{
		return $result;
	}
	curl_close($ch);
}

function sendEmail($to, $subject, $message)
{
	global $db;
	$time = time();
	
	db_insert("emails", [
		'receiver' => $to,
		'subject' => $subject,
		'content' => $message,
		'time' => $time,
	]);

	$from = "ellentaniaphiri@gmail.com";
	//$from = "liwewerobati@gmail.com";
	
	return curl_post("https://adimo-shopping.com/saved/mail.php", ['from' => $from, 'email' => $to, 'subject' => $subject, 'message' => $message], []);
	//return "sent";
}

function sendMessage($to, $message)
{
	global $db;
	$to = $db->real_escape_string($to);
	//$subject = $db->real_escape_string($subject);
	$message = $db->real_escape_string($message);
	$time = time();

	//$from = "ellentaniaphiri@gmail.com";
	
	$ins = $db->query("INSERT INTO `messages`(`id`, `receiver`, `message`, `date`) VALUES (NULL, '$to', '$message', '$time')");
	if (!$ins) {
		file_put_contents('email_log.txt', file_get_contents('email_log.txt')."||".$db->error);
	}
	return curl_post("http://localhost/sms/", ['phone' => $to, 'message' => $message], []);
}


function isPhone($number){
	// Remove any whitespace
    $number = trim($number);
    
    // Pattern for Malawian phone numbers:
    // - Starts with '0' and is exactly 10 digits
    // - Starts with '+265' and is exactly 13 characters
    if (preg_match('/^0\d{9}$/', $number) || preg_match('/^\+265\d{9}$/', $number)) {
        return true;
    }

    return false;
}

function purifyPhone($phone){
	if (strlen($phone) == 10) {
		return "+265".substr($phone, 1);
	}
	elseif (strlen($phone) == 9) {
		return "+265".$phone;
	}
	elseif (strlen($phone) == 12) {
		return "+".$phone;
	}
	return $phone;
}

class JSON{
	public static function dump($data){
		header('Content-Type: application/json');
		echo json_encode($data, JSON_INVALID_UTF8_IGNORE);
	}
}

class SQLiteResult {
	function __construct($result,$isResult=true)
	{
		if ($isResult) {
			$count = 0;
			$store = [];

			while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
				$count += 1;
				foreach ($row as $key => $value) {
					if ($value == null) {
						$row[$key] = "NULL";
					}
				}
				array_push($store, $row);
			}
			$this->store = $store;
			$this->num_rows = $count;
			$this->index = 0;
		}
		else{
			$this->store = $result;
			$this->num_rows = count($result);
			$this->index = 0;
		}
	}

	public function fetch_assoc()
	{
		if ($this->index < $this->num_rows) {
			$this->index += 1;
			return $this->store[$this->index - 1];
		}
		else{
			return false;
		}
	}

	public function fetchArray()
	{
		if ($this->index < $this->num_rows) {
			$this->index += 1;
			return $this->store[$this->index - 1];
		}
		else{
			return false;
		}
	}

	public function getColumnNames()
	{
		$names = [];
		if ($this->num_rows > 0) {
			foreach ($this->store[0] as $key => $value) {
				array_push($names, $key);
			}
		}

		return $names;
	}
}

class mysql_like extends sqlite3{
	public $error = "";
	public $insert_id = 0;

	function __construct($file)
	{
		parent::__construct($file);
	}
	
	public function query($sql)
	{
		if (Strings::contains($sql,"REPEATED(")) {
			$sqlObject = new SqlParser($sql);
			$tdata = $sqlObject->getRepeatedInfo();

			$firstWord = strtolower(explode(" ", trim($sql))[0]);
			if ($firstWord == "select") {
				$data = [];

				$read = parent::query("SELECT {$tdata[1]} FROM {$tdata[0]} ");
				while($row = $read->fetchArray(SQLITE3_ASSOC)){
					if (isset($data[$row[$tdata[1]]])) {
						$data[$row[$tdata[1]]] += 1;
					}
					else{
						$data[$row[$tdata[1]]] = 1;
					}
				}

				//get the clean data
				$clean = [];
				foreach ($data as $key => $value) {
					if ($value > 1) {
						array_push($clean, [
							$tdata[0] => $key,
							'count' => $value
						]);
					}
				}

				return new SQLiteResult($clean, false);
			}
			elseif ($firstWord == "delete") {
				$data = [];
				$ids = [];

				$read = parent::query("SELECT {$tdata[1]},id FROM {$tdata[0]} ");
				while($row = $read->fetchArray(SQLITE3_ASSOC)){
					if (isset($data[$row[$tdata[1]]])) {
						$data[$row[$tdata[1]]] += 1;
						array_push($ids, $row['id']);
					}
					else{
						$data[$row[$tdata[1]]] = 1;
					}
				}

				if (count($ids) > 0) {
					$res = parent::query("DELETE FROM {$tdata[0]} WHERE id IN (".implode(",", $ids).")");
				}
				return true;
			}
			else{

			}
		}
		else{
			$result = parent::query($sql);
			$this->error = $this->lastErrorMsg();
			$chars = explode(" ", trim($sql));

			if (in_array(strtolower($chars[0]), ["select","pragma"])) {
				return new SQLiteResult($result);
			}
			elseif (strtolower($chars[0]) == "insert") {
				return $result;
			}
			else{
				return $result;
			}
		}
	}

	public function real_escape_string($str)
	{
		return $this->escapeString($str);
	}
}

?>