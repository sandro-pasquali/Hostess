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

<?php

require_once("../includes/variables.php");

session_start();

$moderatorId 	= $_SESSION['moderatorId'];

$origMsgId 		= $_GET['origMsgId'];
$responseId		= $_GET['responseId'];

if($origMsgId && $responseId)
  {
    // update Messages (flag send)
    mysql_query("UPDATE messages SET new = '0' WHERE id = $origMsgId");
    
    // now get userId
    $q = mysql_query("SELECT recipient FROM messages WHERE id = $origMsgId");
    $u = mysql_fetch_array($q);
    $hostessId = $u['Recipient'];
    
    // update hostess_history
    mysql_query("INSERT INTO hostess_history (messageId,responseId,senderId,moderatorId) VALUES ($origMsgId,$responseId)");
    
    print '<script type="text/javascript">top.loadUserHistory('.$hostessId.','.$origMsgId.','.$responseId.')</script>';
    exit;
  }

print 'unable to update history';
exit;

?>

</body>
</html>

