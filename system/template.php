<?php
$Template = new Template();

class Template extends Config {
	
	// URL variables
	var $controller, $action, $key, $format;
	var $extra_vars = array();
	var $view;
	
	function Template() {
		
		// initialize superclass so that default routes get initiated
		parent::__construct();
		
		$i = 0;
		if (isset($_GET['request'])) {
			foreach (explode("/", $_GET['request']) as $url_part) {
				switch ($i) {
					case 0: 
						$this->controller = $url_part;
					break;
					case 1:
						$this->action = $url_part;
					break;
					case 2:
						$this->key = $url_part;
					break;
					default:
						array_push($this->extra_vars, $url_part);
					break;
				}
				$i++;			
			}
			
			// set default format
			$this->format = parent::get("default_format");
			// check for other formats to return
			if ($this->key == "" && strpos($this->action, ".") > -1) {
			  
			  // set the format by getting the action right of the dot
			  $this->format = substr($this->action, strpos($this->action, ".")+1);
			  
			  // remove the dot from the action
			  $this->action = substr($this->action, 0, strpos($this->action, "."));
			  
			}
			if ($this->action == "" && strpos($this->controller, ".") > -1) {
			  
			  // set the format by getting the controller right of the dot			  
			  $this->format = substr($this->controller, strpos($this->controller, ".")+1);
			  
			  // remove the dot from the controller			  
			  $this->controller = substr($this->controller, 0, strpos($this->controller, "."));
			  
			  // this is a route of type /controllers/ so set the default action.
			  $this->action = "index";
			}			
			
			if ($this->action == "")
			  $this->action = "index";
			  
		} else {
			// initialize routes
			if ($this->controller == "") {
				$this->controller = parent::getRoute("controller");
			} 
			if ($this->action == "")
				$this->action = parent::getRoute("action");	
				
			if ($this->format == "")
				$this->format = parent::get("default_format");
		}
	}
	
	// render the current page
	function render($string = "") {
		if ($string !== "") { 
			// render text directly.
			print $string;
			return true;
		}


		

		// 1. include controller
		if (!$this->initController()) { return false; }		


		// 2. init Action		
		if (!$this->initAction()) { return false; }

		
		// 3. render view
		// format is obtained from URL.
		if (!$this->renderView()) { return false; }
	}
	
	function generateControllerPath() {
		$sep = parent::get("dir_sep");
		return parent::get("base_dir") . $sep . parent::get("controllers_dir") . $sep . $this->controller . parent::get("extension_with_dot");
	}
	
	function generateViewPath() {
		$sep = parent::get("dir_sep");
		return parent::get("base_dir") 
			.$sep 
			.parent::get("views_dir")
			.$sep 
			.$this->controller
			.$sep 
			.$this->action
			."."
			.$this->format
			.parent::get("extension_with_dot");
	}
	
	function generateModelPath() {
		$sep = parent::get("dir_sep");
		return parent::get("base_dir") 
			.$sep 
			.parent::get("models_dir")
			.$sep 
			.TextHelper::singularize($this->controller)
			.parent::get("extension_with_dot");
	}
	
	function debugRoutes() {
	  print "format: " . $this->format . "<br />";
		print "controller: " . $this->controller . "<br />";
		print "action: " . $this->action . "<br />";
		print "key: " . $this->key . "<br />";			
		print_r($this->extra_vars);
	}
	
	function renderView() {
	  if (file_exists($this->generateViewPath())) {
					
			// load file into view
			
		  $newClass = TextHelper::capitalize($this->controller);
		  eval('$this->' . $newClass . "Controller->load( '" . $this->generateViewPath() ."' );");
		  return true;
		} else {
		  Errors::throwWith404("No view was defined for that URL.");
		  return false;	
		}
	}
	
	function initAction() {
	  // models path
     if (file_exists($this->generateModelPath())) {
  			// include view file
  			include_once($this->generateModelPath());
  			// instantiate the class
			$controllerClass = TextHelper::capitalize($this->controller);
  			$newClass = TextHelper::capitalize(TextHelper::singularize($this->controller));
  			// example: "Welcome::index()"
  			try {
			  // add model to ControllerClass
  			  eval('$this->' . $controllerClass . "Controller->".$newClass ." = new ".$newClass. "();");
			  
			  // fire the appropriate action in the controller
			  // php is stupid
			  if ($this->action == "new") {
			    $this->action = "_new";
			  }
  			  eval('$this->' . $controllerClass . "Controller->" . $this->action . "();");			  
			  // change it back
			  if ($this->action == "_new") {
			    $this->action = "new";
			  }
  			} catch (Exception $e) {
  			  Errors::throwWith404("Action does not exist for that URL.");
  			  return false;
  			}
  			return true;
  		}
  	return true; // controllers don't have to have models
	}
	
	function initController() {
	  if (file_exists($this->generateControllerPath())) {
			

			
			// translate model objects into local objects.
			
			
			// require the file
			$controller = new Controller();
			
			$controller->load($this->generateControllerPath());
			
			// fire the correct method
			$newClass = TextHelper::capitalize($this->controller);			
			// example: "Welcome::index()"
			try {
			  
			  
			  // load controller
  			  eval('$this->' . $newClass . "Controller = new ".$newClass. "();");	 
			  
			  // instantiate view object on controller class
  			  eval('$this->' . $newClass . "Controller->view = new View();");	 
			
			  
			} catch (Exception $e) {
			  Errors::throwWith404("Action does not exist for that URL.");
			  return false;
			}
			return true;
		} else {
			Errors::throwWith404("No controller was defined for that URL.");
			return false;
		}
	}
	
	function formURL($table_name) {
	  $action = "create";
	  if ( $this->action == "edit" ) {
	    $action = "update";
	  }
	  if ($this->controller == "" ) {
		// construct
		$this->Template();  
	  }
	  // is it installed in a subdirectory?
	  $test = substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], "/", 1)-1);
	  if ($test !== $this->controller) {
	    return "/" . $test . "/" . $table_name . "/" . $action;
	  }
    return "/" . $table_name . "/" . $action;
	}
}