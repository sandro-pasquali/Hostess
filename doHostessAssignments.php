<?php

require_once("includes/variables.php");
require_once("hostessAdminOptions.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>	

<title></title>

<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="ALL" />
<meta name="Copyright" content="Copyright (c) Unified Applications Inc." />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<script type="text/javascript"></script>

</head>

<body>
	
<br />

<table cellpadding="4" cellspacing="4" border="1">

<?php


$q = mysql_query("SELECT * FROM moderators");

while($res = mysql_fetch_array($q))
  {
  	$modId = $res['id'];
  	$modName = $res['username'];
  	
    print "<form action=\"assignUser.php\" method=\"post\"><tr><td style=\"background-color:silver;\"><b>$modName</b></td><td><input type=\"hidden\" name=\"mId\" value=\"$modId\"><input type=\"text\" name=\"uInfo\"></td><td><input type=\"submit\" value=\"assign > \"></td>";
    print "</form><form action=\"removeAssignment.php\" method=\"post\"><td><input type=\"hidden\" name=\"mId\" value=\"$modId\">";
    
    $aQ = "select t1.id,t2.username,t2.id as uID from hostess_assignments as t1, profiles as t2 where t1.hostessId = t2.id and t1.moderatorId = $modId";
    
    $a = mysql_query($aQ);
    
    print "Currently Assigned: <select name=\"assignedProfiles\">";
    
    if($a && (mysql_num_rows($a) > 0))
      {
      	
		    while($info = mysql_fetch_array($a))
		      {
		      	$assignmentId = $info['id'];
		      	$userId = $info['uID'];
		      	$uNick = $info['username'];
		      	print "<option value=\"$assignmentId::$userId\">$uNick</option>";
		      }
		    
      }
      
    print "</select>";
      
    print "</td><td>";
    
    print "<input type=\"submit\" value=\"remove assignment\">";
      
    print "</td></tr></form>";
  	
  }


?>


</table>

</body>
</html>
