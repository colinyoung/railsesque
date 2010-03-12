<?php
class Comments extends Controller {
	
	function index() {
	  // set a local variable like this:
	  $this->comments = $this->Comment->findAll();	  
	}
	
	function show() {
		
	}
	
	function delete() {
		$this->comment = $this->Comment->find($this->params["key"]);
		if ($this->comment->delete()) {
	    $this->flash_insert("success", "Comment successfully deleted.");
		  $this->redirect_to("comments_url");
		} else {
		  $this->flash_insert("error", "Error deleting comment.  Comment was not deleted.");
		  $this->redirect_to("comments_url");
		}
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