<?php

require_once("hostessAdminOptions.php");
require_once("includes/variables.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<title>Moderator Report</title>
	<style></style>

	<link href="GUI/runtime/styles/xp/aw.css" rel="stylesheet" type="text/css" ></link>
	<script src="GUI/runtime/lib/library_c.js"></script>
	

  <script type="text/javascript" src="scripts/datePicker.js"></script>

	<!-- grid format -->
	<style>
		.aw-grid-control {height: 60%; width: 100%; border: none; font: menu;}

		.aw-column-0 {width:  80px;}
		.aw-column-1 {width: 50px; background-color: threedlightshadow;}
		.aw-column-2 {text-align: right;}
		.aw-column-3 {text-align: right;}
		.aw-column-4 {width: 100px; background-color: threedlightshadow;}
		.aw-column-5 {text-align: right;}
		.aw-column-6 {text-align: right;}
		.aw-column-7 {text-align: right;}

		.aw-grid-cell {border-right: 1px solid threedshadow;}
		.aw-grid-row {border-bottom: 1px solid threedlightshadow;}
		
		BODY
		  {
		    font-family: Tahoma, Verdana, Arial;
		    font-size: 11px;
		    color: #000000;
		    overflow: hidden;
		  }
		  
	</style>
	
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
	
</script>
	
	
	
</head>
<body onload="init()">
	
<?php

print "<br />";

include("showReportControl.php"); 

print '<hr noshade width="100%" />';
 
// get info for where to write/access stats.xml file
$reportFile = $MODERATOR_FILEPATH."report.xml";
$localReportFile = "moderators/report.xml";

$hostessMail = array();
$allMessages = array();
$moderatorData = array();

$mQ = "SELECT t1.id, t1.username, t2.payout FROM moderators as t1, moderator_payouts as t2 where t1.id = t2.moderatorId and t1.status = 1";
$mR = mysql_query($mQ);

if($startDate && $endDate)
  {
  	while($mInfo = mysql_fetch_assoc($mR))
      {
      	$moderatorId = $mInfo['id'];
      	$moderatorName = $mInfo['username'];
      	$moderatorPayout = $mInfo['payout'];
      	
      	$moderatorData[$moderatorId] = array
				  (
				    'name' => $moderatorName,
				    'total' => 0,
				    'answered' => 0,
				    'unanswered' => 0,
				    'unansweredGreen' => 0,
				    'unansweredYellow' => 0,
				    'unansweredRed' => 0,
				    'completion_perc' => 0,
				    'rate' => $moderatorPayout,
				    'payout' => 0
				  );
      	
      	
		  	// get all hostesses assigned to this moderator
		  	$q = "SELECT t1.hostessId,t2.username FROM hostess_assignments as t1, profiles as t2 WHERE t1.hostessId = t2.id and t1.moderatorId = $moderatorId";
		  	
		  	$qR = mysql_query($q);
		  	
		  	while($res = mysql_fetch_array($qR))
		  	  {
		  	  	$hostessId = $res['hostessId'];
		  	  	
		  	  	// get all mail to this hostess and write it to an array
		  	  	$aq = "SELECT * FROM messages WHERE recipient = $hostessId AND sender > 0 AND date >= '$startDate' AND date <= '$endDate'";
	  	  	
		  	  	$allHostessMail = mysql_query($aq);
		  	  	while($m = mysql_fetch_assoc($allHostessMail))
		  	  	  {
		  	  	  	$hostessMail[$hostessId][] = $m;
		  	  	  }
		  	  	  
		  	  	/* 
		  	  	 * any given hostess may have had x number of previous moderators.
		  	  	 * we want to get only the mail for this moderator.  so we need to 
		  	  	 * check if there were previous moderators for this hostess, if so
		  	  	 * remove the entries in $hostessMail that were responded to by
		  	  	 * another moderator, and that will be the total mails that this
		  	  	 * moderator is responsible for -- count($hostessMail)
		  	  	 */
		  	  	$sq = "SELECT * FROM hostess_history WHERE hostessId = $hostessId AND moderatorId != $moderatorId";
		  	  	$sr = mysql_query($sq);
		  	  	
		  	  	if($sr)
		  	  	  {
    		  	  	while($res = mysql_fetch_assoc($sr))
    		  	  	  {
    		  	  	  	if(isset($hostessMail[$hostessId]))
    		  	  	  	  {
    				  	  	  	foreach($hostessMail[$hostessId] as $k => $info)
    				  	  	  	  {
    				  	  	  	  	if($info['id'] == $res['responseId'])
    				  	  	  	  	  {
    				  	  	  	  	  	// not responsible for this one; remove
    				  	  	  	  	  	unset($hostessMail[$hostessId][$k]);
    				  	  	  	  	  }
    				  	  	  	  }
    		  	  	  	  }
    		  	  	  }
    		  	  }

            if(isset($hostessMail[$hostessId]))
              {
				  	  	foreach($hostessMail[$hostessId] as $f => $finfo)
				  	  	  {
				  	  	  	$moderatorData[$moderatorId]['total'] += 1;
				  	  	  	
				  	  	    $ans = $finfo['new'];
												  	  	  	
										// 1 == unanswered
										if($ans == '1')
										  {
										    $moderatorData[$moderatorId]['unanswered'] += 1;
										    
										    // color coded
										    $hoursBack = intval((time() - strtotime($finfo['date']))/60/60);
										    
										  	if($hoursBack <= 12) 
										  	  {
										  	  	$moderatorData[$moderatorId]['unansweredGreen'] += 1;
										  	  }
										  	else if($hoursBack <= 24)
										  	  {
										  	  	$moderatorData[$moderatorId]['unansweredYellow'] += 1;
										  	  }
										  	else 
										  	  {
										  	  	$moderatorData[$moderatorId]['unansweredRed'] += 1;
										  	  }  
										  }
										else
										  {
										    $moderatorData[$moderatorId]['answered'] += 1;
										  }
				  	  	  } 
		  	  	  }
		  	  }
		  }
  }

/*
 * now build the xml file
 */

$outFile = new DOMDocument('1.0', 'iso-8859-1');
		
$report= $outFile->createElement('report');

foreach($moderatorData as $id => $info)
  {
    $unansDisplay = "<b>[".$info['unanswered']."] <font color=\"#005B00\">".$info['unansweredGreen']."</font>/<font color=\"#DEC61B\">".$info['unansweredYellow']."</font>/<font color=\"red\">".$info['unansweredRed']."</font></b>";
  	
    $completionPerc = ($info['total'] !== 0) ? intval($info['answered'] / $info['total'] * 100) : 0;
    $payout = $info['answered'] * $info['rate'];
  	
  	$moderatorNode = $outFile->createElement('moderator');
  	$moderatorNode->setAttribute("id",$id);
 
    $_moderatorname = $outFile->createElement('hostessname');
    $_moderatorname->appendChild($outFile->createTextNode($info['name']));
  	  
    $_total = $outFile->createElement('total');
    $_total->appendChild($outFile->createTextNode($info['total']));
  	 
    $_answered = $outFile->createElement('answered');
    $_answered->appendChild($outFile->createTextNode($info['answered']));
    
    $_unanswered = $outFile->createElement('unanswered');
    $_unanswered->appendChild($outFile->createTextNode($unansDisplay));
    
    $_completion_perc = $outFile->createElement('completion_perc');
    $_completion_perc->appendChild($outFile->createTextNode($completionPerc.'%'));
  	 
    $_rate = $outFile->createElement('rate');
    $_rate->appendChild($outFile->createTextNode('$'.sprintf("%01.2f", $info['rate'])));
    
    $_payout = $outFile->createElement('payout');
    $_payout->appendChild($outFile->createTextNode('$'.sprintf("%01.2f", $payout)));
  	 
  	$moderatorNode->appendChild($_moderatorname);  
  	$moderatorNode->appendChild($_total);  
  	$moderatorNode->appendChild($_answered);  
  	$moderatorNode->appendChild($_unanswered);  
  	$moderatorNode->appendChild($_completion_perc);  
  	$moderatorNode->appendChild($_rate);  
  	$moderatorNode->appendChild($_payout);  
  	
  	$report->appendChild($moderatorNode);
  }
  
$outFile->appendChild($report);


if(!$handle = fopen($reportFile, 'w+'))
  {
    print "Unable to open user file ($statsFile)";
    exit;
  }
		
if(fwrite($handle, $outFile->saveXML()) === FALSE) 
  {
    print "Cannot write to user file ($statsFile)";
    exit;
  }

print "<center>";
print "<b>MODERATOR REPORT<br /><br />";
print "$startDate > $endDate</b>";
print "</center>";

?>

<hr noshade width="100%" />

	<script>

	var table = new AW.XML.Table;

	//	define data formats
	var str = new AW.Formats.String;
	var num = new AW.Formats.Number;

	//table.setFormats([str, str, num, num, num]);

	//	provide data URL
	table.setURL("<?php print $localReportFile; ?>");

	//	start asyncronous data retrieval
	table.request();

	//	define column labels
	var columns = ["Moderator","Total", "Answered", "Unanswered", "Completion%", "Rate","Payout(USD)"];

	var obj = new AW.UI.Grid;

	obj.setColumnCount(7);

	//	provide column labels
	obj.setHeaderText(columns);

	//	enable row selectors
	obj.setSelectorVisible(true);
	obj.setSelectorText(function(i){return this.getRowPosition(i)});
	obj.setSelectorWidth(25);

	//	set row selection
	obj.setSelectionMode("single-row");

	//	provide external model as a grid data source
	obj.setCellModel(table);

	//	write grid html to the page
	document.write(obj);

	</script>
</body>
</html>