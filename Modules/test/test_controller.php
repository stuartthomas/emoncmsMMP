<?php
	
	
  // no direct access
  defined('EMONCMS_EXEC') or die('Restricted access');
	

  function test_controller()
  {
    global $session,$route;
    $result = false;


    if ($route->action == "view") $result = view("Modules/test/test_view.php",array());

    return array('content'=>$result);
  }
?>
 

  	
 