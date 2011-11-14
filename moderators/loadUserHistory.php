<?php

require_once("../includes/variables.php");

$hostessId = $_GET['hostessId'];
$origMsgId = isset($_GET['origMsgId']) ? $_GET['origMsgId'] : '';
$responseId = isset($_GET['responseId']) ? $_GET['responseId'] : '';

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

$showHistory = $_SESSION['showHistory'];

/*
 * getHostessName
 */
$aQ = mysql_query("SELECT username FROM profiles WHERE id = $hostessId");
$aI = mysql_fetch_array($aQ);
$hostessName = $aI['username'];

$moderatorFilepath = $MODERATOR_FILEPATH.$moderatorName."/";
$userFilepath = $moderatorFilepath.$hostessId;
$relativePath = $moderatorName."/".$hostessId;

/*
 * if a dir does not exist for this moderator, create it.
 */
if(!is_dir($moderatorFilepath))
  {
  	mkdir("$moderatorFilepath");
  }

$messageFile = new DOMDocument('1.0', 'iso-8859-1');
		
// <container>
$container = $messageFile->createElement('container');
		
$container->setAttribute('id','topContainer');
$container->setAttribute('class','menuContainer');
$container->setAttribute('styleSheet','../menus/css/inboxMenu.css');
$container->setAttribute('iconSet','../menus/xml/inboxIcons.xml');

// build xmldoc
$messageFile->appendChild($container);
		
$lastMsgId = 0;

/*
 * get container
 */
$container = $messageFile->getElementsByTagName('container')->item(0);

$msgQuery = "SELECT t1.id,t1.date,t1.sender,t1.recipient,t1.text,t1.new,t2.username FROM messages as t1, profiles as t2 WHERE t1.sender = t2.id AND t1.recipient = $hostessId ORDER BY t1.id DESC";  

$res = mysql_query($msgQuery);

if(@mysql_num_rows($res) < 1)
  {
  	print "There are no messages for $hostessName";
  	exit;
  }
  
// write to the doc
while($r = mysql_fetch_array($res))
  {
  	/*
  	 * store all senders + data,
  	 * to be written to into the document
  	 */
  	 
  	$senderID 					= $r['sender']; 
  	
  	// want to keep track of last loaded message
  	$lastMsgId = $msgId	= $r['id']; 
  	
  	$msgDate 						= $r['date']; 
  	$msgText						= nl2br($r['text']); 
  	$nickName						= $r['username'];
  	$unanswered					= ($r['new'] == '1') ? true : false;
  	
   	/*
  	 * Display of messages depends on two factors: has the message
  	 * been answered?, and, does the user want to see message history
  	 * for messages that HAVE been answered.  
  	 *
  	 * All unanswered messages are displayed, and there is no setting
  	 * to turn this off -- the point is to see unanswered messages, always.
  	 *
  	 * If $showHistory is set to false, only unanswered messages will be shown.
  	 * If $showHistory is set to true, all messages, and response history, are displayed
  	 *
  	 */

    if($unanswered)
  	  {
  	  	/*
  	  	 * no response.  so we color code it based on how old:
  	  	 * now < 12 :: ok.  regular color
  	  	 * 24 > now > 12 :: hm.  yellow
  	  	 * now > 24 :: red
  	  	 */
  	  	 
  	  	$hoursBack =  intval((time() - strtotime($msgDate))/60/60);
  	  	
  	  	if($hoursBack <= 12) 
  	  	  {
  	  	  	$notifyClass = 'notifyGreen';
  	  	  }
  	  	else if($hoursBack <= 24)
  	  	  {
  	  	  	$notifyClass = 'notifyYellow';
  	  	  }
  	  	else
  	  	  {
  	  	  	$notifyClass = 'notifyRed';
  	  	  }
  	  	  
  	    $origMessageText = "<div class=\"msgBox\"><span class=\"$notifyClass\">$msgDate (-$hoursBack)</span>$msgText</div>";
  	    
        $origMessageText .= '<div id="admin'.$msgId.'" class="adminFunctions"><a href="#" onclick="top.composeResponse(\''.$msgId.'::'.$nickName.'\');">RESPOND</a></div>';
  	  }
  	else if($showHistory)
      {
      	$qR = "SELECT t1.text, t1.date FROM messages AS t1, hostess_history AS t2 WHERE t1.id = t2.responseId and t2.messageId = $msgId";
  	    $rQ = mysql_query($qR);
  	      	    
      	$origMessageText = "<div class=\"msgBox\"><span class=\"notifyOff\">$msgDate (ok)</span>$msgText</div>";
      	
      	while($rez = mysql_fetch_array($rQ))
				  {
				  	$respText = nl2br($rez['text']);
				  	$respDate = $rez['date'];
				  	$origMessageText .= "<div class=\"msgResponse\" id=\"response_$msgId\"><div>Response $respDate:</div>$respText</div>";
				  }
		  	
		  	if($ALLOW_MULTIPLE_RESPONSES)
		  	  {
            $origMessageText .= '<div id="admin'.$msgId.'" class="adminFunctions"><a href="#" onclick="top.composeResponse(\''.$msgId.'::'.$nickName.'\');">RESPOND</a></div>'; 
          }
  	  }
  	else
  	  {
  	  	/*
  	  	 * to get here, the message has been responded to, and the viewer
  	  	 * has selected to not see responses/history of conversation
  	  	 */
  	  	continue;
  	  }

  	// check if there is a group for this sender; create if not
  	if(!$container->getElementsByTagName('senderGroup_'.(string)$senderID)->item(0))
  	  {
  	  	$sg = $messageFile->createElement('senderGroup_'.(string)$senderID);
  	  	
  	  	$sg->setAttribute('id','group_'.(string)$senderID);
  	  	$sg->setAttribute('class','rootElement');
  	  	$sg->setAttribute('title',$nickName.' to '.$hostessName);
  	  	$sg->setAttribute('iconState','closed');
  	  	
  	  	// add options for this group
  	  	
  	  	$ops = $messageFile->createElement('div');
  	  	$ops->setAttribute('class','element');
  	  	
  	  	$lnk = $messageFile->createCDATASection('<a onclick="loadProfile('.$senderID.')">VIEW PROFILE FOR '.$nickName.'</a>');
  	  	$ops->appendChild($lnk);
  	  	
  	  	$sg->appendChild($ops);
  	  	
  	  	$container->appendChild($sg);
  	  	
  	  }
  	  
  	$senderGroup = $container->getElementsByTagName('senderGroup_'.(string)$senderID)->item(0);
  	
  	// append msg to sender group
  	$msg = $messageFile->createElement('msg');
  	
  	$msg->setAttribute('id',$msgId);
  	$msg->setAttribute('class','element');
  	$msg->setAttribute('date',$msgDate);
  	    	
  	$msgText = $messageFile->createCDATASection($origMessageText);
  	$msg->appendChild($msgText);
  	
  	$senderGroup->appendChild($msg);
  	
  	$container->appendChild($senderGroup);
  }
  
/* 
 * if there is no $senderGroup set, then there are no 
 * messages to display under the current settings
 */
if(!isset($senderGroup))
  {
  	$sH = ($showHistory) ? '<b>ON</b>' : '<b>OFF</b>';
  	print 'No messages to display. `ShowHistory` is set to '.$sH;
  	exit;
  }

		
$lM = $messageFile->createElement('lastMessage');
$lM->setAttribute('lastMsgId', $lastMsgId);
$container->appendChild($lM);

  
if(!$handle = fopen($userFilepath, 'w+'))
  {
    print "Unable to open user file ($userFilepath)";
    exit;
  }
		
if(fwrite($handle, $messageFile->saveXML()) === FALSE) 
  {
    print "Cannot write to user file ($userFilepath)";
    exit;
  }

  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" xmlns:v="urn:schemas-microsoft-com:vml">

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

<style type="text/css">
	
HTML
  {
    width: 100%;
    height: 100%
    padding: 0px;
    margin: 0px;
    border: 0px;
  }	
  
BODY
  {
    width: 100%;
    height: 100%;
    padding: 0px;
    margin: 0px;
    border: 0px;
  }	
	
.msgBox
  {
    font-size: 12px;
    font-weight: bold;
    color: #000000;
    background-color: #CAD9DB;
    border: 1px #a0a0a0 solid;
    padding: 8px;
    margin-bottom: 4px;
  }	
  
.msgBox SPAN
  {
    float: right;
    border-left: 1px #a0a0a0 solid;
    border-bottom: 1px #a0a0a0 solid;
    font-size: 9px;
    font-family: Verdana, Arial;
    padding: 2px;
    color: #000000;
  }  
  
.msgBox SPAN.notifyGreen
  {
    background-color: green;
  }
  
.msgBox SPAN.notifyYellow
  {
    background-color: yellow;
  }
  
.msgBox SPAN.notifyRed
  {
    background-color: red;
  }
	
.msgBox SPAN.notifyOff
  {
    background-color: #E9E9E9;
  }
	
.msgResponse
  {
    font-family: Tahoma,Helvetica,Arial;
    font-size: 10px;
    color: #8000FF;
    padding: 2px;
    margin-bottom: 2px;
    border-top: 1px #a2a2a2 dashed;
  }	
  
.msgResponse DIV
  {
    color: black;
    font-size: 10px;
    font-weight: bold;
    color: #ff0000;
  }
	
</style>

<script language="Javascript" type="text/javascript" src="../scripts/$Error.js"></script>
<script language="Javascript" type="text/javascript" src="../scripts/XMLHTTP.js"></script>
<script language="JavaScript" type="text/javascript" src="../scripts/Menu.js"></script>
<script language="JavaScript" type="text/javascript" src="../scripts/general.js"></script>

<script language="Javascript">

var origMsgId = '<?php print $origMsgId; ?>';

var composeRef = null;
var MenuRef = new Menu();

function menuLoaded()
  {
  	var lastM = document.getElementById(origMsgId);
    
    if(lastM)
      {
		  	//MenuRef.activate(lastM.parentNode.firstChild);
  	  }
  } 

function init()
  {
	//netscape.security.PrivilegeManager.enablePrivilege("UniversalBrowserRead");

	  // disable text selection in IE
	  //document.onselectstart = function() { return false; }
		// disable text selection in others
		//document.onmousedown = function() { return false; }
		//document.onclick = function() { return true; }
		
		// load the menu
		MenuRef.load('<?php print $relativePath; ?>','mainMenu');
  }
  
function reloadMenu()
  {
  	MenuRef.reload();
  }
  
function loadProfile(pId)
  {
  	composeRef = popUp('loadProfile.php?profileId=' + pId, 'elastic', 700, 600);
  }
  
</script>

</head>
<body onload="init()">

<div id="mainMenu"></div>

</body>
</html>