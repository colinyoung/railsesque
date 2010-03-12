<?php

class Controller extends Template {
	
	var $params;
	
	function __construct() {
	  parent::__construct();
		$this->params = array();
		
		// $_GET
		$this->params["key"] = $this->key;
		
		// $_POST
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
	
	function load($view_file_path) {
	  require_once($view_file_path);
	}
	
	function content_for_layout($view_file_path) {
	  ob_start();
	  require_once($view_file_path);
	  $this->view->content_for("content", ob_get_contents());
	  ob_end_clean();
	}	
	
	function flash_insert($type, $message, $array = "") {
	  $_SESSION["flash"]["type"] = $type;
	  $_SESSION["flash"]["message"] = "<p>" . $message . "</p>";
	  if (is_array($array)) {
	    $_SESSION["flash"]["message"] .= "<ul class=\"list $type\">";
	    foreach($array as $message) {
	      $_SESSION["flash"]["message"] .= "<li>$message</li>";
	    }
	    $_SESSION["flash"]["message"] .= "</ul>";
	  }
	}
	
	function redirect_to($route) {
	  $base_dir = "/";
	  if (parent::get("base_dir") !== "") {
	    $base_dir .= parent::get("base_dir");
	  }
	  header("Location: http://" . $_SERVER['HTTP_HOST'] . $base_dir . $this->parse_route($route));
	}
} 