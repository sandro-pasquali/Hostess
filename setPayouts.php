<?php

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
<meta name="Copyright" content="Copyright (c)" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<script type="text/javascript"></script>

</head>

<body>

<br />

<table cellpadding="4" cellspacing="4" border="1">

<?php

require_once("includes/variables.php");

$payout = .10; // this is the default for new moderators

// handle payout rates
if(isset($_POST['payout']) && isset($_POST['mId']))
  {
  	$pRate = (float)$_POST['payout'];
  	$pMId = $_POST['mId'];
  	
  	if(!mysql_query("UPDATE moderator_payouts SET payout = $pRate WHERE moderatorId = $pMId"))
  	  {
  	  	print "error updating payout rate [ $pMId ] for moderator ID [ $pMId ]";
  	  }
  }

/*
 * This file is designed to allow you to assign payout information moderators.  
 * pay info for moderators is in the seperate table `moderator_payouts`. So we need to:
 * 1. get all the moderator names from moderators
 * 2. check if there is payout info for this moderator in moderator_payouts
 * 3. if not, set a default payout for the moderator
 */

$qM = mysql_query("SELECT * FROM moderators");

while($res = mysql_fetch_array($qM))
  {
  	$modId = $res['id'];
  	$modName = $res['username'];
  	
  	$qP = "SELECT * FROM moderator_payouts WHERE moderatorId = $modId";
  	$pR = mysql_query($qP);
  	$pI = mysql_fetch_array($pR);
  	
  	// moderator not in payouts...
  	if(mysql_num_rows($pR) < 1)
  	  {
		  	mysql_query("INSERT INTO moderator_payouts (moderatorId, payout) VALUES ($modId, $payout)");
  	  }
  	else
  	  {
  	  	$payout = $pI['payout'];
  	  }
  	
    print "<form action=\"setPayouts.php\" method=\"post\"><tr><td>$modName</td><td><input type=\"hidden\" name=\"mId\" value=\"$modId\"><input type=\"text\" name=\"payout\" value=\"$payout\" maxlength=\"4\"></td><td><input type=\"submit\" value=\"set payout\"></td>";
    print "</form></tr>";
  	
  }

?>


</table>

</body>
</html>
