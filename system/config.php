<?php
class Config {
	
	private $host = "localhost";
	private $username = "railsesque";
	private $password = "railsesque";
	private $database = "railsesque";

	var $routes = array();
	
	// "constants"
	private $environment = "development";	
	private $base_dir = "railsesque";
	private $dir_sep = "/";
	private $default_format = "html";	
	
	// okay, you're done configuring
	private $extension_with_dot = ".php";	
	private $controllers_dir = "controllers";
	private $views_dir = "views";
	private $models_dir = "models";	

	
	function __construct() {
		
		error_reporting(E_ALL);
		
		if ($this->host == "" || $this->database == "") {
			print "your database is not added.";
			die();
		}
		// set default routes here
		$this->addRoute("controller", "comments");
		$this->addRoute("action", "index");
		
		// 
		$this->base_file_dir = $_SERVER['DOCUMENT_ROOT'] . $this->dir_sep . $this->base_dir;
	}
	
	function addRoute($key, $value) {
		$this->routes[$key] = $value;
	}
	
	function getRoutes() {
		return $this->routes;
	}
	
	function getRoute($key) {	
		return $this->routes[$key];
	}
	
	function get($property) {
		return $this->$property;	
	}
}