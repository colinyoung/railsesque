<?php

class DB extends Config {
	
	var $table_name = "";
	var $constructed = false;
	var $validations;
	private $db_host = "";
	private $db_username = "";	
	private $db_password = "";
	private $connection;
	
	function DB() {
    $this->db_host = parent::get('host');
    $this->db_username = parent::get('username');
    $this->db_password = parent::get('password'); 
    $this->db_database = parent::get('database');       
    if ($this->table_name == "") {
    	$this->table_name = strtolower(TextHelper::pluralize(get_class($this)));
	  }
	  $this->constructed = true;
	  $this->validations = array();
	}
	
	function connect() {
	  if (!$this->constructed) {
	    $this->DB();
	  }
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
	
	function is_new() {
	  if (!isset($this->id))
	    return false;
	  return true;
	}
	
	function update($table, $key, $arguments) { }	
	
	function delete($table, $key) { }		
	
	function save() {
	  if (!$c = $this->connect())
	    return false;
	  
	  $columnNames = $this->columnNames();
	  $params = array();
	  while ($row = mysql_fetch_array($columnNames, MYSQL_ASSOC)) {
	    
	    // insert defaults (timestamps)
	    if ($row["Field"] == "updated_at") {
		    $params["updated_at"] = @date("Y-m-d H:i:s", time());
		  }
		  if ($row["Field"] == "created_at" && $this->is_new()) {
		    $params["created_at"] = @date("Y-m-d H:i:s", time());
		  }
		  
		  // insert data from params array
      if (isset($this->$row["Field"])) {
        if ($this->$row["Field"] !== "") {
          $params[$row["Field"]] = $this->$row["Field"]; 
        }
		  }
		  
    }
	  // validate (returns array of failures if failed.)
	  
	  $failures = $this->validate($params);
	  if (count($failures) == 0) {
	    $this->query(
    	  $this->sql(
    	    "INSERT INTO",
    	    $this->table_name,
    	    "(", $this->column_names_list(), ")",
    	    "VALUES (", $this->sql_insert_list($params), ")"
    	  )
    	);
	    $this->closeConnection();
    	return true;
	  } else {
	    $this->closeConnection();
	    $this->errors = $failures;
	    return false;	    
	  }
	  // does it have an ID? If so, do an update action.
	}
	
	function validateOnCreate($table, $arguments) { }
	
	function validateOnUpdate($table, $arguments) { }	
	
	function validate($params) { 
	  $model = new $this->name();
	  $failures = array();
	  
	  /* Current validations:
	    - presence_of
	    - length_of
	    - numericality_of
	    - format_of
	    - inclusion_of
	  */
	  // presence_of
    if (array_key_exists("presence_of", $model->validations))	{

  	  foreach($model->validations["presence_of"] as $field) {
  	    if (in_array($field, $params) || @$params[$field] == "") {  	      
  	      $failures[] = "$field (a required field) was not filled out."; 
  	    }
  	  }
  	  
    }
        
    return $failures;
	}
	
	function validates_presence_of() {
    $this->validations["presence_of"] = array();
	  $args = func_get_args();
	  foreach($args as $arg) {
      $this->validations["presence_of"][] = $arg;
	  }
	}
	
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
    $this->closeConnection();
	  return new ResultObject($array, $this->table_name);
	}
	
	function sql() {
	  if (!$c = $this->connect())
	    return false;
	  
	  $query = "";
	  foreach (func_get_args() as $arg) {
	    if (get_magic_quotes_gpc()) {
	      $query .= " " . stripslashes(mysql_real_escape_string($arg));
	    } else {
	      $query .= " " . mysql_real_escape_string($arg);
	    }
	  }
	  return $query;
	}
	
	function query($string) {
	  if (!$c = $this->connect())
	    return false;
	  $r = mysql_query($string);

	  return $r;  
	}
	
	function toResultObjects($mysql_result) {
	  if (!$mysql_result)
	  	die("There was a problem with the database: " . mysql_error());
		
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
	function column_names_list() {
	  $list = "";
	  $columnNames = $this->columnNames();
	  while ($row = mysql_fetch_array($columnNames, MYSQL_ASSOC)) {
	    if ($this->is_new() && $row["Field"] == "id") {
	      continue;
	    }
	    $list .= $row["Field"]. ", ";
	  }
    return substr($list, 0, strlen($list)-2);
	}
	
  function sql_insert_list($params) {
    $list = "";
    
    // add blank id if not exists
    if (!array_key_exists("id", $params)) {
      $params["id"] = "";
    }
    
    foreach($params as $field => $value) {
      if (get_magic_quotes_gpc()) {
        $list .= "'$value', ";
      } else {
        $list .= "'". addslashes($value). "', ";        
      }
    }
    return substr($list, 0, strlen($list)-2);
  }
}