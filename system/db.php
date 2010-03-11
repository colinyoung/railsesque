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
	  parent::__construct();
    $this->db_host = parent::get('host');
    $this->db_username = parent::get('username');
    $this->db_password = parent::get('password'); 
    $this->db_database = parent::get('database');
    if ($this->table_name == "") {
    	$this->table_name = strtolower(TextHelper::pluralize(get_class($this)));
	  }

	  $this->validations = array();
	  
	  // extend the preset regexes (for validates_format_of) here.
	  $this->defaultRegexes = array(
	    ":email" => '/^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$/'
	  );
	  
	  $this->constructed = true;	  
	}
	
	function connect() {
	  if (!$this->constructed) {
	    $this->DB();
	  }
		$this->connection = mysql_connect($this->db_host, $this->db_username, $this->db_password);
		$this->connection = mysql_select_db($this->db_database, $this->connection);
		if ($this->connection) {
			return $this->connection;
		} else {
		  print "Error connecting to database: " . mysql_error();
			return false;
	  }
	}
	// find by ID
	function find($key) {
	  if (!$c = $this->connect())
	    return false;
	  
	  $result = $this->query(
	    $this->sql(
	      "SELECT * FROM",
	      $this->table_name,
	      "WHERE",
	      $this->param_to_queryparam("id", $key)
	    )
	  );
	  if (mysql_num_rows($result) == 0) {
	    return false;
	  }
	  
    $this->closeConnection();
    return new ResultObject($this->result_to_array($result), $this->table_name);
	}
	
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
	
	function delete() { 
	  if (!$c = $this->connect())
	    return false;
	  
	  $r = $this->query(
	    $this->sql(
	      "DELETE FROM",
	      $this->table_name,
	      "WHERE",
	      $this->param_to_queryparam("id", $this->id)
	    )
	  );
	  
	  if (mysql_affected_rows() > 0) {
	    return true;
	  } else {
	    return false;
	  }
	}		
	
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
    	  $sql = $this->sql(
    	    "INSERT INTO",
    	    $this->table_name,
    	    "(", $this->column_names_list(), ")",
    	    "VALUES (", $this->params_to_queryparams($params), ")"
    	  )
    	);
    	if (mysql_affected_rows() == 1) {
  	    $this->closeConnection();
      	return true;    	  
    	} else {
    	  $this->errors = array(mysql_error(), "query was:" . $sql);
  	    $this->closeConnection();    	  
    	  return false;
    	}
    	
	  } else {
	    $this->closeConnection();
	    $this->errors = $failures;
	    return false;	    
	  }
	}
	
	function validateOnCreate($table, $arguments) { }
	
	function validateOnUpdate($table, $arguments) { }	
	
	function validate($params) { 
	  $model = new $this->name();
	  $failures = array();
	  
	  /* Current validations:
	    - presence_of
	    - format_of

	    TODO:
	    - length_of
	    - numericality_of	    
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
    
    // format_of  
    if (array_key_exists("format_of", $model->validations))	{

  	  foreach($model->validations["format_of"] as $pairs_to_validate) {
  	    foreach($pairs_to_validate as $field => $regex) {
  	      if (array_key_exists($regex, $this->defaultRegexes)) {
  	        // pre-existing regex (defined in constructor)
  	        $regex_to_validate = $this->defaultRegexes[$regex];
  	      } else {
  	        // user-submitted regex
  	        $regex_to_validate = $regex;
  	      }
  	      if (!preg_match($regex_to_validate, @$params[$field]))
	          $failures[] = "$field is not formatted correctly.";
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
	
	function validates_format_of($field, $with) {
    if (!isset($this->validations["format_of"]))
      $this->validations["format_of"] = array();
    
	  $this->validations["format_of"][] = array($field => $with);
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
	
  function params_to_queryparams($params) {
    $list = "";
    
    foreach($params as $field => $value) {
      if (get_magic_quotes_gpc()) {
        $list .= "'$value', ";
      } else {
        $list .= "'". addslashes($value). "', ";        
      }
    }
    return substr($list, 0, strlen($list)-2);
  }
  
  function param_to_queryparam($column, $value) {
    if (get_magic_quotes_gpc()) {
      return "$column = '$value'";
    } else {
      return "$column = '". addslashes($value). "'";        
    }
  }  
  
  function result_to_array($mysql_result) {
    $row = mysql_fetch_array($mysql_result, MYSQL_ASSOC);
    return $row;
  }
}