<?php
class ElementObject {
  
  var $type, $innerHTML, $attrs;
  var $constructed = false;
  
  function ElementObject($type, $innerHTML = "", $attrs = array() ) {
    $this->type = $type;
    $this->attrs = $attrs;
    $this->innerHTML = $innerHTML;
    $this->constructed = true;
  }
  
  function setAttributes() { }
  
  function attributeExists() { } 
  
  function mainNode() { }
  
  function toString() {
    if (!$this->constructed)
      $this->ElementObject();
    if ( $this->shouldSelfClose() )
      return "<" . $this->type . $this->formattedAttributes() . " />";
    else
      return "<" . $this->type . $this->formattedAttributes() . ">" 
        . $this->innerHTML
        . "</" . $this->type . ">";
  }
  
  function formattedAttributes() {
    $html = "";

    foreach($this->attrs as $key => $value) {
      $html .= " $key=\"$value\"";
    }
    return $html;
  }
  
  function shouldSelfClose() {
    $self_closing_tags = array(
      "img",
      "link",
      "area",
      "basefont",
      "base",
      "hr",
      "br",
      "input",
      "meta"
    ); # http://www.w3schools.com/xhtml/xhtml_ref_byfunc.asp
    
    if (in_array($this->type, $self_closing_tags))
      return true;
    else
      return false;
  }
}