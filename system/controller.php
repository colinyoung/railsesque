<?php

class Controller extends Config {
	
	function __construct() {
	  $Comment = "hi";
	}
	
	function load($path) {
	  require_once($path);
	}
} 