<?php
class Comments extends Controller {
	
	function index() {
	  $this->hello = "hi";
	  $this->comments = $this->Comment->findAll();
	}
	
	function show() {
		
	}
	
	function delete() {
		
	
	}
	
	function edit() {
		
	}
	
	function create() {
	  $this->comment = $this->Comment->_new($this->params["comment"]);
	  if ($this->comment->save()) {
		print "Comment successfully saved.";
	  } else {
		print "Comment did not pass validation.";  
	  }
	}	
	
	function _new() {
	  $this->comment = $this->Comment->_new(); 
	}
}