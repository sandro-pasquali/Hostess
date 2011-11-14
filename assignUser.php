<?php

require_once("includes/variables.php");

$moderatorId = $_POST['mId'];
$profileId = trim($_POST['uInfo']);

if($profileId == '')
  {
  	print "search term is blank<br /><br />";
		print "Click <a href=\"doHostessAssignments.php\">here</a> to go back.";
  	exit;
  }

if((string)intval($profileId) != (string)$profileId)
  {
  	// assume nickname if a string
  	$q = @mysql_query("SELECT id FROM profiles where username = '$profileId'");
  }
else
  {
  	$q = @mysql_query("SELECT id FROM profiles where id = $profileId");
  }

if(@mysql_num_rows($q) < 1)
  {
  	print "unable to find profile with id/nickname of [ $profileId ]<br /><br />";
		print "Click <a href=\"doHostessAssignments.php\">here</a> to go back.";
  	exit;
  }		

// normalize
$pInf = mysql_fetch_array($q);
$profileId = $pInf['id'];

// now check if another moderator has already been assigned this id
		
$cq = "select t1.id, t2.username from hostess_assignments as t1, moderators as t2 where t1.moderatorId = t2.id and t1.hostessId = $profileId and t1.moderatorId != $moderatorId";

$c = mysql_query($cq);
		
if(mysql_num_rows($c) > 0)
  {
  	$inf = mysql_fetch_array($c);
  	
	  print "profile has already been assigned to moderator [ ".$inf['name']." ]<br><br>"; 	
		print "Click <a href=\"doHostessAssignments.php\">here</a> to go back.";
		exit;
  }
		  
// now check if this moderator has already been assigned this id
		
$c = mysql_query("select id from hostess_assignments where moderatorId = $moderatorId and hostessId = $profileId");
if(mysql_num_rows($c) > 0)
  {
	  print "profile has already been assigned to this moderator<br /><br />";
		print "Click <a href=\"doHostessAssignments.php\">here</a> to go back.";
		exit;
  }
		
$u = mysql_query("insert into hostess_assignments (moderatorId,hostessId) values ($moderatorId, $profileId)");

header("Location:doHostessAssignments.php");

?>