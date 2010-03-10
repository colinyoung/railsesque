<?php
class Comments extends Controller {
	
	function index() {
	  $this->view->hello = "hi";
	  $this->view->comments = $this->Comment->findAll();
	}
	
	function show() {
		
	}
	
	function delete() {
		
	
	}
	
	function edit() {
		
	}
	
	function _new() {
	  $this->view->comment = $this->Comment->_new(); 
	}
}