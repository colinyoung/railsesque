<?php

$Sessions = new Sessions();

class Sessions {
	
	function __construct() {
		session_start();			
	}
	
	function login() { }
	
	function logout() { }
}