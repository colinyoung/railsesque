<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html>
  <head>
    <title><?php echo $this->view->yield("title") ? $this->view->yield("title") : "$this->controller/$this->action" ?></title>
    <?php echo $this->view->stylesheet_link_tag("application"); ?>
    <?php echo $this->view->javascript_include_tag(":all"); ?>    
  </head>
  <body>
    <?php
    if (array_key_exists("flash", $_SESSION)) {
      ?>
      <div class="flash <?php echo $_SESSION['flash']['type']; ?>">
        <?php echo $_SESSION['flash']['message']; ?>
      </div>
      <?php
      // clear after printing
      unset($_SESSION["flash"]);
    }
    ?>
    <?php echo $this->view->yield(); ?>
  </body>
</html>
