<?php
$Sessions = new Session();
class Sessions {
	
	function __construct() {
		session_start();			
	}
	
	function login() { }
	
	function logout() { }
}