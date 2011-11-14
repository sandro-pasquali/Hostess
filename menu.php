<?php

require_once("includes/variables.php");

session_start();

/*
 * admins viewing this page will not have session->moderatorId || session->moderatorName set.
 * admins set session->lastModeratorId || session->lastModeratorName
 *
 * so, check if admin or moderator, and set appropriately
 */

if(isset($_SESSION['adminOK']) && ($_SESSION['adminOK'] == "confirmed"))
  {
		$moderatorId = $_SESSION['lastModeratorId'];
		$moderatorName = $_SESSION['lastModeratorName'];
  }
else
  {
		$moderatorId = $_SESSION['moderatorId'];
		$moderatorName = $_SESSION['moderatorName'];
  }

$h = (isset($_SESSION['showHistory'])) ? $_SESSION['showHistory'] : $_GET['showHistory'];
if($h)
  {
  	$_SESSION['showHistory'] = ($h=='on') ? true : false;
  }

?>


<html>

<head>
<title>Untitled</title>
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="-1" />
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="ALL" />
<meta name="Copyright" content="Copyright (c)" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

	<style type="text/css"> body, html {width:100%; height:100%; margin:0px; padding: 0px;} </style>
<link href="GUI/runtime/styles/xp/aw.css" rel="stylesheet" type="text/css"></link>
	<style type="text/css">
		.aw-grid-control {height: 90%; width: 100%; margin: 0px; border: none; font: menu;}
		.aw-row-selector {text-align: center}

		.aw-column-0 {width: 50px;text-align:center;}
		.aw-column-1 {width: 50px;text-align:center;}
		.aw-column-2 {width: 150px;text-align:center;}
		.aw-column-3 {width: 55px; text-align:center;}
		.aw-column-4 {width: 55px; text-align:center; background-color:#F7F7F7; font-weight: bold; color: red;}
		.aw-column-5 {width: 55px; text-align:center; background-color:#F7F7F7; font-weight: bold; color: #DEC61B;}
		.aw-column-6 {width: 55px; text-align:center; background-color:#F7F7F7; font-weight: bold; color: #005B00;}
		.aw-column-7 {width: 60px; text-align:center;}
		
		.aw-grid-cell {border-right: 1px solid threedlightshadow;}
		.aw-grid-row {border-bottom: 1px solid threedlightshadow;}
	</style>

<style type="text/css">

BODY
  {
    font-family: Tahoma, Verdana, Arial;
    font-size: 11px;
    font-weight: bold;
    color: #000000;
    overflow: hidden;
  }
 
LI
  {
    margin: 4px;
  }
  
A
  {
    padding: 4px;
  }  
  
A:hover
  {
    background-color: #c0c0c0;
    cursor: pointer;
    cursor: hand;  
  }

#header
  {
    width: 96%;
    background-color: #909090;
    border-bottom: 1px #a9a9a9 solid;
    font-family: Tahoma, Helvetica, Arial;
    font-weight: bold;
    font-size: 12px;
    padding: 8px;
    margin-bottom: 8px;
    text-align: center;
  }

</style>

	<script src="GUI/runtime/lib/library_c.js"></script>


<script language="JavaScript" type="text/javascript" src="scripts/general.js"></script>

<script type="text/javascript">
	  
function showUser(id)
  {
  	top.loadUserHistory(id);
  }
  
function loadProfile(pId)
  {
  	popUp('moderators/loadProfile.php?profileId=' + pId, 'elastic', 700, 600);
  }
	
</script>

</head>
<body>

<?php

print '<div id="header">'.strtoupper($moderatorName).'</div><center>';

$hist = ($_SESSION['showHistory']==true) ? '<a href="menu.php?showHistory=off">TURN <b>OFF</b> HISTORY</a>' : '<a href="menu.php?showHistory=on">TURN <b>ON</b> HISTORY</a>';

print $hist.' | <a href="index.php" target="_parent">HOME</a> | <a href="javascript:void(top.frames[1].document.location.href = \'showModeratorStats.php\');">STATS</a> | <a href="logout.php" target="_parent">LOG OUT</a></center><br />';

/*
 * get all usernames and show list
 */
$aQ =  "SELECT t1.hostessId, t2.username FROM hostess_assignments AS t1, profiles AS t2 WHERE t1.hostessId = t2.id and t1.moderatorId = $moderatorId";
 
$q = mysql_query($aQ);

/*
 * get info and sort for display
 */
 
$menuList = Array();

while($r = mysql_fetch_array($q))
  {
  	$profileId = $r['hostessId'];
  	$nick = $r['username'];
  	
  	/*
  	 * get hostess info (state/country)
  	 */
  	$aQ = "select state,country from profiles where id = $profileId";
  	$aQR = mysql_query($aQ);
  	$aQD = mysql_fetch_assoc($aQR);
  	
  	$hostessState = $aQD['state'];
  	$hostessCountry = $aQD['country']; 
  	
  	/*
  	 * now get some stats on the moderation success of this user
  	 */
  	
  	// get total messages
  	$mQ = mysql_query("SELECT count(*) as cnt FROM messages WHERE recipient = $profileId AND sender > 0");
  	$mXXXX = mysql_fetch_array($mQ);
    $totalMessages = $mXXXX['cnt'];
    
  	// get last response Date
  	$mQ = mysql_query("SELECT t1.date from messages as t1, hostess_history as t2 where t1.id = t2.responseId order by t2.id DESC LIMIT 0,1");
  	$mXXX = mysql_fetch_array($mQ);
  	$lastActivity = $mXXX['date'];
  	
  	// get count of new messages
  	$mQ = mysql_query("SELECT count(*) as cnt FROM messages WHERE sender > 0 AND recipient = $profileId AND date >= '$lastActivity'");
  	$mXX = mysql_fetch_array($mQ);
    $newMessages = $mXX['cnt'];
    
  	// get count of answered messages
  	$mQ = mysql_query("SELECT count(*) as cnt FROM messages WHERE sender > 0 AND recipient = $profileId AND new = '0'");
  	$mX = mysql_fetch_array($mQ);
    $answered = $mX['cnt'];
    
  	// get count of unanswered messages
  	$mQ = mysql_query("SELECT date FROM messages WHERE sender > 0 AND new = '1' AND recipient = $profileId");

		$moderatorTotals = Array
		  (
		    'unansweredGreen' => 0,
		    'unansweredYellow' => 0,
		    'unansweredRed' => 0
		  );
		  
    while($dI = mysql_fetch_assoc($mQ))
      {
      	$hoursBack = intval((time() - strtotime($dI['date']))/60/60);
 	      
		  	if($hoursBack <= 12) 
		  	  {
		  	  	$moderatorTotals['unansweredGreen'] += 1; 
		  	  }
		  	else if($hoursBack <= 24)
		  	  {
		  	  	$moderatorTotals['unansweredYellow'] += 1; 
		  	  }
		  	else 
		  	  {
		  	  	$moderatorTotals['unansweredRed'] += 1; 
		  	  } 
      }
    
    /*
     * a little strange, but quicker than doing a sort on all entries later
     */
    if(!isset($menuList[$totalMessages]))
      {
      	$menuList[$totalMessages] = Array();
      }
      
    $menuList[$totalMessages][] = Array
      (
        'profileId' 			=> $profileId,
        'hostessState' 		=> $hostessState,
        'hostessCountry'	=> $hostessCountry,
        'nick' 						=> $nick,
        'newMessages' 		=> $newMessages,
        'uR'							=> $moderatorTotals['unansweredRed'],
        'uY'							=> $moderatorTotals['unansweredYellow'],
        'uG'							=> $moderatorTotals['unansweredGreen'],
        'answered'				=> $answered
      );
  }

// lose any empty elements
//unset($menuList[0]);

//$menuList = array_reverse($menuList, true); 

$modDataOut = '';

print '<script type="text/javascript">var myData = [';

$rowCount = 0;
$profileIdHash = array();
foreach($menuList as $totalMessages => $a)
  {
  	foreach($a as $k => $list)
  	  {
		    $profileId 			= $list['profileId'];
		    $hostessState   = $list['hostessState'];
		    $hostessCountry = $list['hostessCountry']; 
		    $nick 					= $list['nick'];
		    $newMessages 		= $list['newMessages'];
		    $uG 						= $list['uG'];
		    $uY 						= $list['uY'];
		    $uR 						= $list['uR'];
		    $answered				= $list['answered'];
		    /*
		  	print "<li><a href=\"#\" onclick=\"showUser($profileId)\" id=\"$profileId\">$nick ($newMessages new, $unanswered unanswered, $totalMessages total)</a> | <a href=\"#\" onclick=\"loadProfile($profileId)\">[ profile ]</a><br />";
		  	*/
		  	
			  $modDataOut .= "['$hostessState', '$hostessCountry', '$nick', '$totalMessages', '$uR', '$uY', '$uG', '$answered'],";

        $profileIdHash[$rowCount] = $profileId;
        ++$rowCount;
		  }
  }

$modDataOut = substr($modDataOut,0,-1);

print $modDataOut;  
  
print '];';


/*
 * now print profile id hash
 */
print 'var profileIdHash = [';
foreach($profileIdHash as $row => $id)
  {
  	print "$id,";
  }
print '];';

print '</script>';


?>
		
<script type="text/javascript">
		var myColumns = [
			"State", "Country", "Hostess", "Total", "Urgent", "Late", "Recent", "Answered"
		];

	var obj = new AW.UI.Grid;

	//	define data formats
	var str = new AW.Formats.String;
	var num = new AW.Formats.Number;

	obj.setCellFormat([str, str, str, num, num, num, num, num]);

	//	provide cells and headers text
	obj.setCellText(myData);
	obj.setHeaderText(myColumns);

	//	set number of rows/columns
	obj.setRowCount(<?php print $rowCount; ?>);
	obj.setColumnCount(8);

	//	enable row selectors
	obj.setSelectorVisible(true);
	obj.setSelectorText(function(i){return this.getRowPosition(i)+1});

	//	set headers width/height
	obj.setSelectorWidth(28);
	obj.setHeaderHeight(20);

	//	set row selection
	obj.setSelectionMode("single-row");

	//	set click action handler
	obj.onCellClicked = function(event, col, row)
	  {
	  	//alert(col);
	  	//alert(this.getCellText(col, row));
	  	
	  	var profileID = profileIdHash[row];
	  	
	  	if(col == 2)
	  	  {
	  	    // show profile
	  	    loadProfile(profileID)
	  	  }
	  	else
	  		{
	  			// show inbox
	  	    showUser(profileID);
	  		}
	  		
      return(true);	    
	  };

	//	write grid html to the page
	document.write(obj);

</script>

</body>
</html>
