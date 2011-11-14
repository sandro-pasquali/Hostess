<?php

require_once("hostessAdminOptions.php");
require_once("includes/variables.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:v="urn:schemas-microsoft-com:vml">
<head>
	<title>Moderator Stats</title>
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
 
$modDir = $MODERATOR_FILEPATH.$moderatorName;
$statsFile = $modDir."/stats.xml";
$localStatsFile = "moderators/".$moderatorName."/stats.xml";

// get info for where to write/access stats.xml file

// if no directory, create it
if(!is_dir($modDir))
  {
  	// try to create the dir
  	if(!mkdir($modDir))
  	  {
  	  	print 'unable to create moderator directory';
  	  	exit;
  	  }
  }

// get the payout info for this moderator
$mPQ = mysql_query("SELECT payout FROM moderator_payouts WHERE moderatorId = $moderatorId");

if($mPQ)
  {
    $mPD = mysql_fetch_assoc($mPQ);
  }
  
if(!isset($mPD))
  {
  	"There is no payout information for this moderator [ $moderatorName ].";
  	exit;
  }
  
$mPayout = $mPD['payout'];

// the document will be built out of these data structures
$hostessMail = array();
$messageCounts = array();
$answered = array();
$unanswered = array();
$responses = array();
$hostessNames = array();
$moderatorTotals = array
  (
    'total' => 0,
    'answered' => 0,
    'unanswered' => 0,
    'unansweredGreen' => 0,
    'unansweredYellow' => 0,
    'unansweredRed' => 0,
    'completion_perc' => 0,
    'rate' => $mPayout,
    'payout' => 0
  );

if($startDate && $endDate && $moderatorId)
  {
  	// get all hostesses assigned to this moderator
  	$q = "SELECT t1.hostessId,t2.username FROM hostess_assignments as t1, profiles as t2 WHERE t1.hostessId = t2.id and t1.moderatorId = $moderatorId";
  	$qR = mysql_query($q);
  	
  	while($res = mysql_fetch_array($qR))
  	  {
  	  	$hostessId = $res['hostessId'];
  	  	$hostessNames[$hostessId] = $res['username'];
  	  	
  	    // sort answered/unanswered info
  	    $answered[$hostessId] = array();
  	    $unanswered[$hostessId] = array();
  	  	
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
  	    
  	    /*
  	     * there is a slight difference between the next two queries.  It would
  	     * seem that the total message count - total response count would
  	     * give you the totalUnanswered, but that is not so.  It is possible that
  	     * this hostess was allowed to respond multiple times to the same message.
  	     * this would create a response count of, say, three (3), but that would only 
  	     * represent one (1) message response.
  	     */
  	    
  	    if(isset($hostessMail[$hostessId]))
  	      {
  	        $messageCounts[$hostessId] = count($hostessMail[$hostessId]);
		  	    foreach($hostessMail[$hostessId] as $k => $info)
		  	      {
						   	$ans = $info['new'];
						  	  	  	
							 	// 1 == unanswered
							 	if($ans == '1')
							 	  {
							      $unanswered[$hostessId][] = $hostessMail[$hostessId][$k];
							 	  }
							 	else
							 	  {
							      $answered[$hostessId][] = $hostessMail[$hostessId][$k];
								  }
		  	      }
  	      
		  	    /*
		  	     * now get response count for this hostess
		  	     */
		  	    $rq = "SELECT count(t1.id) AS cnt FROM hostess_history as t1, messages as t2 WHERE t1.messageId = t2.ID AND t1.hostessId = $hostessId AND t1.moderatorId = $moderatorId AND t2.date >= '$startDate' AND t2.date <= '$endDate'";
		  	    $rr = mysql_query($rq);
		  	    if($rr && (mysql_num_rows($rr) > 0))
		  	      {
		  	        $rC = mysql_fetch_array($rr);
		            $responses[$hostessId] = $rC['cnt'];
		          }
		        else
		          {
		            $responses[$hostessId] = 0;
		          }
  	      }      
  	  }
  }
 
/*
 * now build the xml file
 */

$outFile = new DOMDocument('1.0', 'iso-8859-1');
		
$stats = $outFile->createElement('stats');

foreach($hostessMail as $hostessId => $messages)
  {
    $totalAnswered = count($answered[$hostessId]);
    $totalMessages = $messageCounts[$hostessId];
    $completionPerc = ($totalMessages !== 0) ? intval($totalAnswered / $totalMessages * 100) : 0;
    

    $payout = $totalAnswered * $moderatorTotals['rate'];
    
    /*
     * want information on unanswered == color coding
     */
    $totalUnanswered = count($unanswered[$hostessId]);
    $unansDisplay = "<b>[$totalUnanswered] ";
    $aUn = array
      (
        'green' => 0,
        'yellow' => 0,
        'red' => 0
      );
    foreach($unanswered[$hostessId] as $uk => $uM)
      {
      	$hoursBack = intval((time() - strtotime($uM['date']))/60/60);
 	      
		  	if($hoursBack <= 12) 
		  	  {
		  	  	$aUn['green'] += 1; 
		  	  	$moderatorTotals['unansweredGreen'] += 1; 
		  	  }
		  	else if($hoursBack <= 24)
		  	  {
		  	  	$aUn['yellow'] += 1; 
		  	  	$moderatorTotals['unansweredYellow'] += 1; 
		  	  }
		  	else 
		  	  {
		  	  	$aUn['red'] += 1; 
		  	  	$moderatorTotals['unansweredRed'] += 1; 
		  	  }  
      }
    
    $unansDisplay .= '<font color="#005B00">'.$aUn['green'].'</font>/<font color="#DEC61B">'.$aUn['yellow'].'</font>/<font color="red">'.$aUn['red'].'</font></b>';
  	
  	$hostessNode = $outFile->createElement('hostess');
  	$hostessNode->setAttribute("id",$hostessId);
 
    $_hostessname = $outFile->createElement('hostessname');
    $_hostessname->appendChild($outFile->createTextNode($hostessNames[$hostessId]));
  	  
    $_total = $outFile->createElement('total');
    $_total->appendChild($outFile->createTextNode($totalMessages));
    $moderatorTotals['total'] += $totalMessages; 
  	 
    $_answered = $outFile->createElement('answered');
    $_answered->appendChild($outFile->createTextNode($totalAnswered));
    $moderatorTotals['answered'] += $totalAnswered; 
    
    $_unanswered = $outFile->createElement('unanswered');
    $_unanswered->appendChild($outFile->createTextNode($unansDisplay));
    $moderatorTotals['unanswered'] += $totalUnanswered; 
    
    $_completion_perc = $outFile->createElement('completion_perc');
    $_completion_perc->appendChild($outFile->createTextNode($completionPerc.'%'));
  	 
    $_rate = $outFile->createElement('rate');
    $_rate->appendChild($outFile->createTextNode('$'.sprintf("%01.2f", $moderatorTotals['rate'])));
    
    $_payout = $outFile->createElement('payout');
    $_payout->appendChild($outFile->createTextNode('$'.sprintf("%01.2f", $payout)));
  	 
  	$hostessNode->appendChild($_hostessname);  
  	$hostessNode->appendChild($_total);  
  	$hostessNode->appendChild($_answered);  
  	$hostessNode->appendChild($_unanswered);  
  	$hostessNode->appendChild($_completion_perc);  
  	$hostessNode->appendChild($_rate);  
  	$hostessNode->appendChild($_payout);  
  	
  	$stats->appendChild($hostessNode);
  }
  
  
  
  
  
/*
 * add a line with totals (aka moderator total for all hostess)
 */

$unEntry = "<b>[".$moderatorTotals['unanswered']."] ";

foreach($moderatorTotals as $n => $v)
  {
    switch($n)
      {
      	case 'completion_perc':
          $completionPerc = ($moderatorTotals['total'] !== 0) ? intval($moderatorTotals['answered'] / $moderatorTotals['total'] * 100) : 0;
      	  $moderatorTotals[$n] = (string)$completionPerc."%";
      	break;
      	
      	case 'rate':
		  	 $moderatorTotals[$n] = sprintf("%01.2f", $v);
      	break;
      	
      	case 'payout':
      	 $payout = $moderatorTotals['answered'] * $moderatorTotals['rate'];
		  	 $moderatorTotals[$n] = sprintf("%01.2f", $payout);
		  	break;
		  	
		  	case 'unansweredGreen':
		  	  $unEntry .= '<font color="#005B00">'.$v.'</font>';
		  	break;
		  	
		  	case 'unansweredYellow':
		  	  $unEntry .= '/<font color="#DEC61B">'.$v.'</font>';
		  	break;
		  	
		  	case 'unansweredRed':
		  	  $unEntry .= '/<font color="red">'.$v.'</font></b>';
		  	break;
		  	
		  	default:
		  	break;
  	  }
  }
  
$hostessNode = $outFile->createElement('hostess');

$_hostessname = $outFile->createElement('hostessname');
$_hostessname->appendChild($outFile->createTextNode('TOTAL'));
  	  
$_total = $outFile->createElement('total');
$_total->appendChild($outFile->createTextNode($moderatorTotals['total']));
  	 
$_answered = $outFile->createElement('answered');
$_answered->appendChild($outFile->createTextNode($moderatorTotals['answered']));
    
$_unanswered = $outFile->createElement('unanswered');
$_unanswered->appendChild($outFile->createTextNode($unEntry));
    
$_completion_perc = $outFile->createElement('completion_perc');
$_completion_perc->appendChild($outFile->createTextNode($moderatorTotals['completion_perc']));
  	 
$_rate = $outFile->createElement('rate');
$_rate->appendChild($outFile->createTextNode('$'.$moderatorTotals['rate']));
    
$_payout = $outFile->createElement('payout');
$_payout->appendChild($outFile->createTextNode('$'.$moderatorTotals['payout']));
  	 
$hostessNode->appendChild($_hostessname);  
$hostessNode->appendChild($_total);  
$hostessNode->appendChild($_answered);  
$hostessNode->appendChild($_unanswered);  
$hostessNode->appendChild($_completion_perc);  
$hostessNode->appendChild($_rate);  
$hostessNode->appendChild($_payout);  
 
$stats->appendChild($hostessNode); 

$outFile->appendChild($stats);

if(!$handle = fopen($statsFile, 'w+'))
  {
    print "Unable to open user file ($statsFile)";
    exit;
  }
		
if(fwrite($handle, $outFile->saveXML()) === FALSE) 
  {
    print "Cannot write to user file ($statsFile)";
    exit;
  }


/*
 *do the header
 */

$moderatorTotals['completion_perc'] = ($moderatorTotals['total'] !== 0) ? intval($moderatorTotals['answered'] / $moderatorTotals['total'] * 100) : 0;
$moderatorTotals['payout'] = $moderatorTotals['answered'] * $moderatorTotals['rate']; 

print "<center>";
print "<b>".strtoupper($moderatorName)."<br />$startDate > $endDate</b>";
print "</center>";

?>

<hr noshade width="100%" />

	<script>

	var table = new AW.XML.Table;

	//	define data formats
	var str = new AW.Formats.String;
	var num = new AW.Formats.Number;

	//table.setFormats([str, num, num, num, num, num, num, num]);

	//	provide data URL
	table.setURL("<?php print $localStatsFile; ?>");

	//	start asyncronous data retrieval
	table.request();

	//	define column labels
	var columns = ["Hostess","Total", "Answered", "Unanswered", "Completion%", "Rate","Payout(USD)"];

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