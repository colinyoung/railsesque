<?php

class View extends Template {

	var $view;
	var $variables;
	
	function __construct() {
		$variables = array();
	}
	/*
	
	-- load file into the view.
	
	
	*/
	function load($path) {
	  require_once($path);
	}
	
	function buildForm($resultObject) {
	  $f = array();
	  foreach($resultObject as $column => $value) {
	    $f[] = new FormFieldObject($resultObject, $column);
	  }
	  return $f;
	}
	
	function startForm($resultObject) {
	  $this->ob = $resultObject;
	  ob_start();
	}
	
	function endForm() {
    $target = $this->formURL($this->ob->table_name);
	$form_tag = new ElementObject("form", NULL, array("method" => "post", "action" => $target));
    $form_tag = str_replace("</form>", "", $form_tag->toString());

	  $form_contents = str_replace("  ", "", ob_get_contents());
	      
	  ob_end_clean();
	  return $form_tag . "\n" . trim($form_contents) . "\n" . "</form>";
	}	
	
	function link_to($text = "", $route, $options = "") {
	  if (!is_array($options))
	    $options = array();
	  
	  $url = "";
	  $basedir = $this->getConfig("base_dir");
	  
	  if ($basedir !== "") {
	    $url .= "/" . $basedir;
	  }
	  if (array_key_exists("key", $options)) {
      $url .= $this->parse_route($route, $options["key"] );
    } else {    	  
      $url .= $this->parse_route($route);
    }
    
    if ($text == "") {
      $text = $url;
    }    
    
    $a = new ElementObject("a", $text, array("href" => $url));
	  return $a->toString();
	}
}