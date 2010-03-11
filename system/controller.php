<?php

class Controller extends Template {
	
	var $params;
	
	function __construct() {
		$this->params = array();
		foreach($_POST as $arrayKey => $array) {

			foreach($_POST as $key => $req) {	
			  if (get_magic_quotes_gpc()) {
				  foreach($req as $request_key => $request_val) {
				    $request_val = stripslashes($request_val); 
				  }
				}
        $this->params[$key] = $req;
			}
		}
	}
	
	function load($path) {
	  require_once($path);
	}
	
	function flash_insert($type, $message) {
	  $_SESSION["flash"] = "$type: $message";
	}
	
	function redirect_to($route) {
	  $base_dir = "/";
	  if (parent::get("base_dir") !== "") {
	    $base_dir .= parent::get("base_dir");
	  }
	  header("Location: http://" . $_SERVER['HTTP_HOST'] . $base_dir . $this->parse_route($route));
	}	
} 