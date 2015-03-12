<?php
class DB_MySQL  {

	var $DB;
	var $querycount			=	0;

	function DB_MySQL($DB) {
		$this->DB=$DB;
	}

	function error() {
		return $this->DB->error();
	}

	function geterrno() {
		return $this->DB->errno();
	}

	function insert_id() {
		$id = $this->DB->insert_id();
		return $id;
	}

	function connect($servername, $dbusername, $dbpassword, $dbname, $usepconnect=0) {
		if($usepconnect) {
			if(!@mysql_pconnect($servername, $dbusername, $dbpassword)) {
				die("Could not Connect");
			}
		} else {
			if(!@mysql_connect($servername, $dbusername, $dbpassword)) {
				die("Could not Connect @ ".$this->geterrno());
			}
		}

		$this->DB->select_db($dbname);
	}

	function select_db($dbname) {
		return $this->DB->select_db($dbname);
	}

	function query($sql,$type = '') {
		$query = $this->DB->query($sql);
		if(!$query && $type != 'SILENT') {
			die('MySQL Query Error: '.$sql);
		}
		$this->querycount++;
		return $query;
	}

	function affected_rows($sql)
	{
		$this->query($sql);

		return $this->DB->affected_rows();
	}

	function fetch_array($query) {
		$result = $this->query($query);
		return $result->fetch_array();
	}
	//bug
	function fetch_row($query) {
		$query = $this->DB->fetch_row($query);
		return $query;
	}

	function fetch_one_array($query) {
		$record = $this->fetch_array($query);
		return $record;
	}

	function fetch_one($query) {
		$record = $this->fetch_array($query);
		return $record[0];
	}

	function num_rows($query) {
		if ($stmt = $this->DB->prepare($query)){
			$stmt->execute();
			$stmt->store_result();
			return $stmt->num_rows;
		}
		return -1;//error
	}

	function free_result($query) {
		$query = $this->DB->free_result($query);
		return $query;
	}

	function close() {
		return $this->DB->close();
	}

	function version() {
		return $this->DB->get_server_info();
	}
	
	function fetch_all($sql,$column='') {
		$arr = array();
		$result = $this->DB->query($sql);
		if (!empty($result)&&$result->num_rows > 0) {
    	// output data of each row
    		while($row = $result->fetch_assoc()) {
    			if($column!=''){
    				$arr[]=$row["$column"];
    			}else{
    				$arr[]=$row;
    			}
    		}
    	}
		return $arr;
	}

	function compile_db_insert_string($data)
	{
		$field_names = "";
		$field_values = "";

		foreach ($data as $k => $v)
		{
			$field_names .= "$k,";
			$field_values .= "'$v',";
		}

		$field_names = preg_replace( "/,$/" , "" , $field_names );
		$field_values = preg_replace( "/,$/" , "" , $field_values );

		return array('FIELD_NAMES' => $field_names,
					 'FIELD_VALUES' => $field_values,
					 );
	}

	function compile_db_update_string($data)
	{
		$return_string = "";

		foreach ($data as $k => $v)
		{
			if(is_array($v))
			{
				$return_string .= $k . "=".$v['0'].",";
			}else if($v=='NOW()'){
				$return_string .= $k . "= NOW() ,";	
			}else
			{
				$return_string .= $k . "='".$v."',";
			}
		}

		$return_string = preg_replace( "/,$/" , "" , $return_string );//remove the last comma

		return $return_string;
	}

	function insert_sql( $tbl , $arr ,$action="INSERT")
	{
		$dba = $this->compile_db_insert_string( $arr );

		$sql = "{$action} INTO {$tbl} ({$dba['FIELD_NAMES']}) VALUES ({$dba['FIELD_VALUES']})";
		
		return $sql;
	}

	function update_sql($tbl , $arr , $where='')
	{
		$dba	=	$this->compile_db_update_string( $arr );

    	$query = "UPDATE {$tbl} SET $dba";
    	
    	if ( $where )
    	{
    		$query .= " WHERE ".$where;
    	}

		return $query;
	}
	//new
	function signUp($username,$email,$password,$group)
	{
		if($group=='user'){
    		$query = "CALL signUp(TRUE,'$email','$username','$password',@error,@id);";
    	}else{
    		$query = "CALL signUp(FALSE,'$email','$username','$password',@error,@id);";
    	}
    	$this->query($query);
    	$error=$this->fetch_one("select @error;");
    	$uid=$this->fetch_one("select @id;");
		return array($uid,$error);
	}
	//new
	function typeSplit($typel)
	{
    	$query = "CALL splitType('$typel');";
    	$this->query($query);
    	$types=$this->fetch_all("select mtype from mtypel;");
    	$this->query("drop table mtypel;");
		return $types;
	}
	//new
	function conIdSplit($conidl)
	{
		//echo "<script>console.log('conidl:".$conidl."');</script>";
    	$query = "CALL splitReCon('$conidl');";
    	$this->query($query);
    	$conids=$this->fetch_all("select con from reconl;");
    	//echo "<script>console.log('conid:".$conids[0]["con"]."');</script>";
    	$this->query("drop table reconl;");
		return $conids;
	}
	
}
?>