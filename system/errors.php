<?php
class Errors extends Config {
  
  function throwWith404($msg) {
    if (parent::get("environment") !== "production") {
      
      // in non-production mode, print error and exit
      print $msg; exit(0);
    } else {
      // in production mode, header a 404 with a generic error message.
      header("HTTP/1.0 404 Not Found"); 
      echo "<h1>404 Not Found</h1>";
      echo "The page that you have requested could not be found.";
      exit(0);
    }
  }
  
}