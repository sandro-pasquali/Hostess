<?php

require_once("includes/variables.php");

$q = mysql_query("SELECT * FROM moderators");

while($res = mysql_fetch_array($q))
  {
    $userFile = new DOMDocument('1.0', 'iso-8859-1');
		
    // <container>
    $container = $userFile->createElement('container');
    $container->setAttribute('id','userContainer');
  
    $u = mysql_query("select t1.profileId, t2.username from hostess_assignments as t1, profiles as t2 where t1.profileId = t2.id and t1.hostessId = ".$res['id']);
    
    while($us = mysql_fetch_array($u))
      {
  
        $user = $userFile->createElement('user');
    
        $user->setAttribute('id',$us['profileId']);
    
  	    $userName = $userFile->createCDATASection($us['NickName']);
  	    $user->appendChild($userName);
  	    
  	    $container->appendChild($user);
  	  } 	
  }


?>

