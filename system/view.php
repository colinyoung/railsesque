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
	
	function link_to($text = "", $route, $key = "", $options = "") {
	  if (!is_array($options))
	    $options = array();
	  
	  $url = "";
	  $basedir = $this->getConfig("base_dir");
	  
	  if ($basedir !== "") {
	    $url .= "/" . $basedir;
	  }
	  if ($key !== "") {
      $url .= $this->parse_route($route, $key);
    } else {    	  
      $url .= $this->parse_route($route);
    }
    
    if ($text == "") {
      $text = $url;
    }    
    
    $a = new ElementObject("a", $text, array_merge($options, array("href" => $url) ));
	  return $a->toString();
	}
	
	function content_for($item, $content) {
	  $this->$item = $content;
	}
	
	function yield($item = "content") {
	  if (isset($this->$item))
	    return $this->$item;
	  else
	    return false;
	}
	
	
	// please don't look at these
	function stylesheet_link_tag($item = ":all") {
    $link_elements = "";	  
	  if ($item == "" || $item == ":all") {
	    $sep = parent::get('dir_sep');
	    $styles_dir = parent::get('stylesheets_dir');
	    $files = scandir($styles_dir);
	    foreach($files as $file) {
	      $ext = substr($file, strrpos($file, "."));
	      if ($ext == ".css") {
    	    $link_element = new ElementObject("link", NULL, array(
    	      "href" => "/" . parent::get("base_dir") . "/" . parent::get("stylesheets_dir") . "/" . $file,
    	      "rel" => "stylesheet"
    	    ));
	        $link_elements .= $link_element->toString() . "\n";
	      }
	    }
	  } else {
	    if (!strpos($item, ".css")) {
	      $item .= ".css";
	    }
	    $link_element = new ElementObject("link", NULL, array(
	      "href" => "/" . parent::get("base_dir") . "/" . parent::get("stylesheets_dir") . "/" . $item,
	      "rel" => "stylesheet"
	    ));
      $link_elements .= $link_element->toString() . "\n";
	  }
	  return $link_elements;
	}
	
	function javascript_include_tag($item = ":all") {
    $script_elements = "";
	  if ($item == "" || $item == ":all") {
	    $sep = parent::get('dir_sep');
	    $styles_dir = parent::get('javascripts_dir');
	    $files = scandir($styles_dir);
	    foreach($files as $file) {
	      $ext = substr($file, strrpos($file, "."));
	      if ($ext == ".js") {
	        $script_element = new ElementObject("script", NULL, array("src" => "/" . parent::get("base_dir") . "/" . parent::get("javascripts_dir") . "/" . $file));
	        $script_elements .= $script_element->toString() . "\n";
	      }
	    }
	  } else {
	    if (!strpos($item, ".js")) {
	      $item .= ".js";
	    }
	    $script_element = new ElementObject("script", NULL, array("src" => "/" . parent::get("base_dir") . "/" . parent::get("javascripts_dir") . "/" . $item));
      $script_elements .= $script_element->toString() . "\n";
	  }
	  return $script_elements;
	}	
	
  function date_format($mysql_datetime_string, $format = "") {
	  $date = explode("-", substr($mysql_datetime_string, 0, 10));
	  $time = explode(":", substr($mysql_datetime_string, 11));
	  // hour, minute, second, month, day, year
	  $ts = mktime(
	    $time[0], $time[1], $time[2],
	    $date[1], $date[2], $date[0]
	  );
	  if ($format == "") {
	    $format = 'F j, Y \a\t g:i a';
	  }
	  return date($format, $ts);
	}
	
	function time_ago_in_words($mysql_datetime_or_timestamp) {
	  $format = 'D, d M Y H:i:s O'; // http://php.net/manual/en/class.datetime.php
	  
	  // convert if not numeric, our vendor class requires a TS
    if (!is_numeric($mysql_datetime_or_timestamp)) {
      $dt = $mysql_datetime_or_timestamp;
	    $time_ago = new TimeAgo($this->date_format($dt, $format));      
      return $time_ago->inWords();
    } else {
	    $time_ago = new TimeAgo( date($format, $mysql_datetime_or_timestamp) );
      return $time_ago->inWords();
    }
	}		
}