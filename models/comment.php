<?php

class Comment extends DB {
  function __construct() {
    $this->validates_presence_of("email", "content");
  }
}