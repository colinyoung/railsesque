<?php

class ResultObject extends DB {
  
  var $name, $table_name, $used, $used_labels, $submit_button_printed;
  
  function __construct($mysql_result, $table_name = "") {
    $isResult = false;
	  $this->table_name = $table_name;	
    $this->singular_table_name = TextHelper::singularize($table_name);
    $this->used = array();
    $this->used_labels = array();
    $this->submit_button_printed = false;
    
    while (@$row = mysql_fetch_array($mysql_result, MYSQL_ASSOC)) {
      $columns = array();
      foreach ($row as $column => $value) {
        $columns[$column] = $value;
      }
      $this->rows[] = new ResultObject($columns);
      $isResult = true;
    }
    
    if (!$isResult)
      $this->loadObject($mysql_result);
  }
  
  function loadObject($hash) {
    foreach ($hash as $column => $value) {
      eval('$this->' . $column . ' = $value;');
    }
  }
  
  function get($property) {
    return $this->$property;
  }
  
  function used($property) {
    return in_array($property, $this->used);
  }
  
  function setUsed($property) {
    $this->used[] = $property;
  }
  function usedLabel($property) {
    return in_array($property, $this->used_labels);
  }
  
  function setUsedLabel($property) {
    $this->used_labels[] = $property;
  }
  
  function setSubmitButtonPrinted() {
    $this->submit_button_printed = true;
  } 
}