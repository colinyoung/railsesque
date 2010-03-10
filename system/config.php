<?php
class Config {
	
	var $host = "";
	var $username = "";
	var $password = "";
	var $database = "";

	var $routes = array();
	
	// "constants"
	var $environment = "development";	
	var $base_dir = "";
	var $dir_sep = "/";
	var $extension_with_dot = ".php";
	var $controllers_dir = "controllers";
	var $views_dir = "views";
	var $models_dir = "models";	
	var $default_format = "html";
	
	function __construct() {
		
		error_reporting(E_ALL);
		
		// set default routes here
		$this->addRoute("controller", "comments");
		$this->addRoute("action", "index");
		
		// 
		$this->base_dir = $_SERVER['DOCUMENT_ROOT'] . $this->dir_sep . $this->base_dir;
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