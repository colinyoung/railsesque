<?php
class Config {
	
	private $host, $username, $password, $database;

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
	private $config_dir = "config";		

	
	function __construct() {
		
		// global PHP Configuration Options (every script)
		error_reporting(E_ALL);
		
		
		$this->base_file_dir = $_SERVER['DOCUMENT_ROOT'] . $this->dir_sep . $this->base_dir;
		// application event hooks
		
		// get database information
		$spyc = Spyc::YAMLLoad($this->base_file_dir . $this->dir_sep . $this->config_dir . $this->dir_sep . "database.yml");
		$this->parse_config($spyc[$this->environment]);
		
		// comment out the following block if you aren't using databases.
		if ($this->host == "" || $this->database == "") {
			print "your database is not added.";
			die();
		}
		// set default routes here
		$this->addRoute("controller", "comments");
		$this->addRoute("action", "index");
		
	}
	
	function parse_config($array) {
	  foreach($array as $key => $value) {
	    $this->$key = $value;
	  }
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