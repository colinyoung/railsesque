<?php

class Controller extends Config {
	
	var $params;
	
	function __construct() {
		$this->params = array();
		foreach($_POST as $arrayKey => $array) {

			foreach($_POST as $key => $req) {	
				$this->params[$key] = $req;
			}
		}
	}
	
	function load($path) {
	  require_once($path);
	}
} 