<?php

require_once("includes/variables.php");

session_start();

$_SESSION['showHistory'] = (isset($_POST['showHistory']) && ($_POST['showHistory'] == 'on')) ? true : false;

/*
 * this check will be done on initial login, or when the
 * moderator (or admin) returns from a stats report. 
 *
 * So there are three paths:
 * 1. Initial login -> loadInbox.php
 * 2. Moderator views own stats and returns to inbox
 * 3. Admin views a moderator's stats and returns to inbox
 *
 * 1 -> this is a moderator logging in from index.php, 
 *      so POST->username/POST->password is set;
 * 2 -> this is a moderator returning from stats page, already logged in,
 *      so session->moderatorName, session->moderatorPass are set
 * 3 -> this is an admin who has run showModeratorReports.php and selected
 *      a moderator, so session->lastModeratorName ONLY is set
 *
 * so, for cases 1 & 2(moderators), check POST->username/password, 
 * then check session->username, session->password
 *
 * for case 3, check if an admin (session->adminOK), and if so,
 * then grab session->lastModeratorName, and ignore pass.
 */

$loginOK = false;

if(isset($_SESSION['adminOK']) && ($_SESSION['adminOK'] == "confirmed"))
  {
  	$u = $_SESSION['lastModeratorName'];
  	
		$qs = "select id,username,password from moderators where username='$u'";
		
		$q = mysql_query($qs);
		
		if($q && (mysql_num_rows($q) > 0))
		  {
		    $res = mysql_fetch_array($q);
		    $moderatorId = $res['id'];
		    $moderatorName = $res['username'];
		    $moderatorPass = $res['password'];
		    
		    $_SESSION['lastModeratorId'] = $moderatorId;
		    $_SESSION['lastModeratorName'] = $moderatorName;
		    
		    $loginOK = true;
      }
  }
else
  {
  	$moderatorId = $_POST['moderatorId'];
		$p = $_POST['password'];
		$h = $_POST['showHistory'];
		
		$p = ($p != '') ? $p : $_SESSION['moderatorPass'];
		
		$qs = "select username,password from moderators where id=$moderatorId and password='$p'";
		
		$q = mysql_query($qs);
		
		if($q && (mysql_num_rows($q) > 0))
		  {
		    $res = mysql_fetch_array($q);
		    $moderatorName = $res['username'];
		    $moderatorPass = $res['password'];
		    
		    $_SESSION['moderatorId'] = $moderatorId;
		    $_SESSION['moderatorName'] = $moderatorName;
		    $_SESSION['moderatorPass'] = $moderatorPass;

		    $loginOK = true;
		  }
  }
  
if($loginOK)
  {
  	
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

<script type="text/javascript" src="scripts/general.js"></script>
<script type="text/javascript">

var composeRef = null;

function loadUserHistory(hostessId,origMsgId,responseId)
  {
  	var orig = (origMsgId) ? '&origMsgId=' + origMsgId : '';
  	var resp = (responseId) ? '&responseId=' + responseId : '';
  	
  	var quer = '/HOSTESS/moderators/loadUserHistory.php?hostessId=' + hostessId + orig + resp;
  	
  	frames['MainDisplay'].document.location.href = quer;

  }
	
function composeResponse(msgId)
  {
  	composeRef = popUp('compose.php?msgInf=' + msgId, 'elastic', 700, 600);
  }  
	
function closeComposeDialog()
  {
  	composeRef.close();
  }	
	
</script>

</head>


<FRAMESET COLS="35%,*">
	<FRAME NAME="Menu" SRC="menu.php?moderatorId=<?php print $moderatorId; ?>&moderatorName=<?php print $moderatorName; ?>" TITLE="Menu" />
	<FRAME NAME="MainDisplay" SRC="showModeratorStats.php" TITLE="MainDisplay" border="3" />
</FRAMESET>


</html>  


<?php

  }
else
  {
  	print "Sorry, I don't see that name in the moderator list, or the password is incorrect.";
  }
?>