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
	    $this->flash_insert("success", "Comment successfully saved.");
		  $this->redirect_to("comments_url");
	  } else {
	    $this->flash_insert("error", "Error saving comment:", $this->comment->errors);
		  $this->redirect_to("comments_url");	    
	  }
	}	
	
	function _new() {
	  $this->comment = $this->Comment->_new(); 
	}
}