<?php


require_once("includes/variables.php");

$moderatorId = $_POST['mId'];
$assignmentInfo = explode("::",$_POST['assignedProfiles']);

$assignmentId = $assignmentInfo[0];
$profileId = $assignmentInfo[1];

// make sure this is a valid user
if($profileId != '')
  {
		$q = @mysql_query("SELECT id,username FROM profiles where id = '$profileId'");
		
		if(mysql_num_rows($q) < 1)
		  {
		  	print "unknown user with id of [ $profileId ]<br><br>";
        exit;
		  }

    // delete assignment
    if(mysql_query("DELETE FROM hostess_assignments WHERE id = $assignmentId"))
      {
		    $u = mysql_fetch_array($q);
				print "Deleted ".$u['username']." assignment. <a href=\"doHostessAssignments.php\">Click here to go back</a>";
		  }
		else
		  {
		  	print "Unable to delete record.  Got error from dbase > ".mysql_error();
		  	exit;
		  }
  }
else
  {
    print "bad id sent.";
    exit;	
  }

?>