<?php

require_once("hostessAdminOptions.php");

require_once("includes/variables.php");

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

	<style type="text/css"> body, html {width:100%; height:100%; padding: 0px;} </style>
<link href="GUI/runtime/styles/xp/aw.css" rel="stylesheet" type="text/css"></link>
	<style type="text/css">
		.aw-grid-control {height: 60%; width: 100%; margin: 0px; border: none; font: menu;}
		.aw-row-selector {text-align: center}

		.aw-column-0 {width: 120px;text-align:center;}
		.aw-column-1 {width: 120px;text-align:center;}
		.aw-column-2 {width: 55px; text-align:center; background-color:#F7F7F7; font-weight: bold; color: red;}
		.aw-column-3 {width: 55px; text-align:center; background-color:#F7F7F7; font-weight: bold; color: #DEC61B;}
		.aw-column-4 {width: 55px; text-align:center; background-color:#F7F7F7; font-weight: bold; color: #005B00;}
		.aw-column-5 {width: 55px; text-align:center;}
		.aw-column-6 {width: 65px; text-align:center;}
		.aw-column-6 {width: 65px; text-align:center;}
				
		.aw-grid-cell {border-right: 1px solid threedlightshadow;}
		.aw-grid-row {border-bottom: 1px solid threedlightshadow;}
	</style>

<script type="text/javascript" src="GUI/runtime/lib/library_c.js"></script>
<script type="text/javascript" src="scripts/datePicker.js"></script>
  
<script type="text/javascript">
	
var startingDate = null;
var endingDate = null;	
	
function init()
  {
		startingDate = new calendar1(document.forms['datePicker'].elements['startDate']);
		startingDate.year_scroll = false;
    startingDate.time_comp = true;
    
		endingDate = new calendar1(document.forms['datePicker'].elements['endDate']);
		endingDate.year_scroll = false;
		endingDate.time_comp = true;
  }
  
function showStats(typ)
  {
  	switch(typ)
  	  {
  	  	case 'recipient':
  	  	  var f = document.getElementById('statsform');
  	  	  f.action = 'showModeratorStats.php';
  	  	  f.submit();
  	  	break;
  	  	
  	  	case 'sender':
  	  	  var f = document.getElementById('statsform');
  	  	  f.action = 'showSenderStats.php';
  	  	  f.submit();
  	  	break;
  	  	
  	  	default:
  	  	break;
  	  	
  	  	
  	  }
  }
	
</script>

</head>
<body onload="init()">	
	
	
<?php

print "<br />";

include("showStatsControl.php"); 

print '<hr noshade width="100%" />';

$hostessInfo = Array
  (
    
  );

if($startDate && $endDate && $moderatorId)
  {
  	// get all hostesses assigned to this moderator
  	$q = "SELECT t1.hostessId,t2.username FROM hostess_assignments as t1, profiles AS t2 WHERE t1.hostessId = t2.id AND t1.moderatorId = $moderatorId";
  	$qR = mysql_query($q);

    while($inf = mysql_fetch_assoc($qR))
      {
      	$hostessID = $inf['hostessId'];
      	$nick = $inf['username'];
      	
        $hostessInfo[$hostessID] = Array
          (
            'hostessNick' => $nick,
            'senders'     => Array()
          );
        
        /*
         * now get stats on all mail sent to this hostess.
         * NOTE that we are checking if sender profile still
         * exists in profiles database -- if not, don't bother.
         */
        $sQ = "SELECT t1.new,t1.date,t1.sender,t2.username FROM messages as t1, profiles as t2 WHERE t1.sender = t2.id AND t1.recipient = $hostessID AND t1.date >= '$startDate' AND t1.date <= '$endDate'";
               
        $sR = mysql_query($sQ);
        
        while($sInf = mysql_fetch_assoc($sR))
          {
          	$senderID = $sInf['sender'];
          	$date = $sInf['date'];
          	$nick = $sInf['username'];
          	$isNew = ($sInf['new'] == 1) ? true : false;
          	
          	if(!isset($hostessInfo[$hostessID]['senders'][$senderID]))
          	  {
          	  	$hostessInfo[$hostessID]['senders'][$senderID] = Array
          	  	  (
          	  	    'senderNick' 				=> $nick,
          	  	    'total' 						=> 0,
          	  	    'totalUnanswered' 	=> 0,
          	  	    'totalUrgent' 			=> 0,
          	  	    'totalLate' 				=> 0,
          	  	    'totalRecent'				=> 0
          	  	  );
          	  }
          	  
          	
          	// update total
          	$hostessInfo[$hostessID]['senders'][$senderID]['total']++;
          	
          	// update unanswered & urgency values
          	if($isNew)
          	  {
          	  	$hostessInfo[$hostessID]['senders'][$senderID]['totalUnanswered']++;

		            // update urgency values
				      	$hoursBack = intval((time() - strtotime($date))/60/60);
						  	if($hoursBack <= 12) 
						  	  {
						  	  	$hostessInfo[$hostessID]['senders'][$senderID]['totalRecent'] += 1; 
						  	  }
						  	else if($hoursBack <= 24)
						  	  {
						  	  	$hostessInfo[$hostessID]['senders'][$senderID]['totalLate'] += 1; 
						  	  }
						  	else 
						  	  {
						  	  	$hostessInfo[$hostessID]['senders'][$senderID]['totalUrgent'] += 1; 
						  	  } 
						  }
          }
      }
  }
else
  {
    print "no results.";
    exit;	
  }

$modDataOut = '';
$rowCount = 0;
print '<script type="text/javascript">var myData = [';

foreach($hostessInfo as $aID => $aMailInfo)
  {
  	$aNick = $aMailInfo['hostessNick'];
  	$sNick = '';
  	$total = 0;
  	$totalUnanswered = 0;
  	$totalUrgent = 0;
  	$totalLate = 0;
  	$totalRecent = 0;
  	
  	foreach($aMailInfo['senders'] as $senderID => $senderInfo)
  	  {
  	  	$sNick = $senderInfo['senderNick'];
  	  	$total = $senderInfo['total'];
  	  	$totalUnanswered = $senderInfo['totalUnanswered'];
  	  	$totalUrgent = $senderInfo['totalUrgent'];
  	  	$totalLate = $senderInfo['totalLate'];
  	  	$totalRecent = $senderInfo['totalRecent'];
  	  	
				$modDataOut .= "['$sNick', '$aNick', '$totalUrgent', '$totalLate', '$totalRecent', '$total', '$totalUnanswered'],";
        ++$rowCount;
  	  }
  }
  
$modDataOut = substr($modDataOut,0,-1);

print $modDataOut;

print '];';
print '</script>';

print "<center>";
print "<b>".strtoupper($moderatorName)."<br />$startDate > $endDate</b>";
print "</center>";
print '<hr nohade width="100%" />';
?>
		
<script type="text/javascript">
		var myColumns = [
			"Sender", "Hostess", "Urgent", "Late", "Recent", "Total", "Unanswered"
		];

	var obj = new AW.UI.Grid;

	//	define data formats
	var str = new AW.Formats.String;
	var num = new AW.Formats.Number;

	obj.setCellFormat([str, str, num, num, num]);

	//	provide cells and headers text
	obj.setCellText(myData);
	obj.setHeaderText(myColumns);

	//	set number of rows/columns
	obj.setRowCount(<?php print $rowCount; ?>);
	obj.setColumnCount(7);

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
	  	
	  	var profileID = this.getCellText(0,row);
	  	
	  	if(col == 1)
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
