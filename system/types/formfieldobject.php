<?php
class FormFieldObject extends ResultObject {
  
  var $obj, $field, $html;
  
  function __construct($resultObject, $column) {
    $this->obj = $resultObject;
    $this->field = $column;
  }
  
  function label($field, $paragraph = false) {
    // labels do not setUsed, they setUsedLabel
    if (!$this->obj->usedLabel($field)) {
      // label object
      $label = new ElementObject("label", Inflector::humanize($field), array("name" => "{$this->obj->singular_table_name}[$field]"));
      
      
      // this is an array of used labels in the parent class.
      $this->obj->setUsedLabel($field);
      
      // print it
      if ($paragraph)
        return "<p>" . $label->toString() . "</p>";
      else
        return $label->toString();
    }
    return false;
  }
  
  function text_field($field) {
    if (!$this->obj->used($field)) {

      // object
      $element = new ElementObject("input", NULL,
        array(
            "name" => "{$this->obj->singular_table_name}[$field]",
            "type" => "text"
        )
      );
      
      // print it
      $this->html = $element->toString();
      $this->field_name = $field;    
      // this is an array of used labels in the parent class.
      $this->obj->setUsed($field);
      return $this->html;
    } 
    return false;
  }
  
  function text_area($field, $options = array() ) {
    $default_options = array(
        "name" => "{$this->obj->singular_table_name}[$field]",
        "rows" => "7",
        "cols" => "40"
    );
    if (!$this->obj->used($field)) {
      $element = new ElementObject("textarea", "",
        array_merge($options, $default_options)
      );
      $this->html = $element->toString();
      $this->field_name = $field;
      $this->obj->setUsed($field);
      return $this->html;
    }
    return false;
  }  
  
  function checkbox($field, $options = array() ) {
    $default_options = array(
        "name" => "{$this->obj->singular_table_name}[$this->field]",
        "type" => "checkbox"
    );
    if (!$this->obj->used($field)) {
      $element = new ElementObject("input", "",
        array_merge($options, $default_options)
      );
      $this->html = $element->toString();
      $this->field_name = $field;      
      $this->obj->setUsed($field);
      return $this->html;
    }
    return false;
  }
  
  function submit($text = "Save changes", $paragraph = true, $options = array() ) {
    $default_options = array(
        "type" => "submit",
        "value" => $text
    );
    if (!$this->obj->submit_button_printed) {
      $element = new ElementObject("input", NULL,
        array_merge($options, $default_options)
      );
      $this->html = $element->toString(); 
      $this->obj->setSubmitButtonPrinted();
      if ($paragraph)
        return "<p>" . $this->html . "</p>";
      else
        return $this->html;
    }
    return false;
  }   
  
  function wrap_label($formFieldObject) {
    
    // it will not be an object if the field has already been printed.
    if (!is_object($formFieldObject)) {
      return false;
    }    
    if (!$this->usedWrap) {
      // label object
      $innerHTML = $formFieldObject->html;
      
      $label = new ElementObject("label", Inflector::humanize($formFieldObject->field_name) . "<br />" . $innerHTML, array("for" => "{$this->obj->singular_table_name}-{$formFieldObject->field_name}"));
      
      $this->usedWrap = true;
      
      return "<p>" . $label->toString() . "</p>";
    }
    return false;
  }
  
  function toString() {
    return $this->html;
  }
  
  function setUsed() {
    $this->used = true;
  }
  
  function setUsedLabel() {
    $this->usedLabel = true;
  }  
}