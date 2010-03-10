<?php

class TextHelper {
	
	function titlecase($string) {
		$arr = explode(" ", $string);
		$title = "";
		for ($i = 0; $i < count($arr); $i++) {
		  $title .= strtoupper(substr($arr[$i], 0, 1)) . substr($arr[$i], 1);
		  
		  // only add spaces if it isn't the last word.
		  if ($i != count($arr) -1)
		    $title .= " ";
		}
		return $title;
	}
	
	function capitalize($string) {
		return strtoupper(substr($string, 0, 1)) . substr($string, 1);
	}
	
	function singularize($string) {
	  return Inflector::singularize($string);
	}
	
	function pluralize($string) {
	  return Inflector::pluralize($string);
	}	
}