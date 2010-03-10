<?php

class DB extends Config {
	
	var $table_name = "";
	var $db_host = "";
	var $db_username = "";	
	var $db_password = "";		
	var $constructed = false;
	var $connection;
	
	function DB() {
    $this->db_host = parent::get('host');
    $this->db_username = parent::get('username');
    $this->db_password = parent::get('password'); 
    $this->db_database = parent::get('database');       
    
    $this->table_name = strtolower(TextHelper::pluralize(get_class($this)));
    
	  $this->constructed = true;    
	}
	
	function connect() {
	  if (!$this->constructed)
	    $this->DB();
		$this->connection = mysql_connect($this->db_host, $this->db_username, $this->db_password);
		$this->connection = mysql_select_db($this->db_database, $this->connection);
		if ($this->connection)
			return $this->connection;
		else
			return false;
	  
	}
	
	function findByKey($table, $key) { }
	
	function findAll($where = "") {   
	  
	  // connect to DB
	  if (!$c = $this->connect())
	    return false;
	  
	  // return all results for $this->table_name
	  $result = $this->query(
	    $this->sql(
	      "SELECT * FROM",
	      $this->table_name
	    )
	  );
	  
	  $this->closeConnection();
	  return $this->toResultObjects($result);
	}	
	
	function update($table, $key, $arguments) { }	
	
	function delete($table, $key) { }		
	
	function validateOnCreate($table, $arguments) { }
	
	function validateOnUpdate($table, $arguments) { }	
	
	function validate($table, $arguments) { }
	
	function _new() {
	  if (!$c = $this->connect())
	    return false;
	  $columnNames = $this->columnNames();
	  
	  $array = array();
	  
    while ($row = mysql_fetch_array($columnNames, MYSQL_ASSOC)) {
      $array[$row["Field"]] = "";
    }
    
    // if there's an argument of one array,
    // merge it with the columns.
	  if (func_num_args() > 0) {
	    $args = func_get_args();
      $array = array_merge($array, $args[0]);
	  }    

	  return new ResultObject($array, $this->table_name);
	}
	
	function sql() {
	  if (!$c = $this->connect())
	    return false;
	  
	  $query = "";
	  foreach (func_get_args() as $arg) {
	    $query .= " " . mysql_real_escape_string($arg);
	  }
	  return $query;
	}
	
	function query($string) {
	  if (!$c = $this->connect())
	    return false;
	  $r = mysql_query(mysql_real_escape_string($string));
	  return $r;  
	}
	
	function toResultObjects($mysql_result) {
	  $arr = array();
	  while ($row = mysql_fetch_array($mysql_result, MYSQL_ASSOC)) {
	    $arr[] = new ResultObject($row, $this->table_name);
	  }
	  return $arr;
	}
	
	function closeConnection() {
	  @mysql_close($this->connection);
	}
	
	function columnNames() {
	  $result = $this->query(
	    $this->sql(
	      "SHOW COLUMNS FROM",
	      $this->table_name
	    )
	  );
	  return $result;
	}
}