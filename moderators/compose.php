<?php

require_once("../includes/variables.php");

$msgInf = explode("::",$_GET['msgInf']);

$msgId = $msgInf[0];
$nickName = $msgInf[1];

if(!$msgId || !$nickName) { exit; }

$res = mysql_query("SELECT * FROM messages WHERE id = $msgId");

$inf = mysql_fetch_array($res);

$text 				= $inf['text'];
$senderId 		= $inf['sender'];
$recipientId 	= $inf['recipient'];

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

<style type="text/css">

#chars
  {
    border: 0px;
    background-color: #000000;
    font-weight: bold;
    font-size: 12px;
    width: 30px;
    padding: 4px;
  }

.charCountBad
  {
    color: #ff0000;
  }
  
.charCountGood
  {
    color: #00ff00;
  }
	
</style>

<script type="text/javascript" src="../scripts/general.js"></script>
<script type="text/javascript">
	
var validCharCount = 100;	

function getCount()
  {
  	var inp = document.getElementById('outMsg');
  	
    var sp = / /gi;
    var nl = /\n/gi;

  	var cnt = inp.value.replace(sp,'').length;
  	cnt = inp.value.replace(nl,'').length;
  	
  	return(cnt);
  }
	
function trackCharacters()
  {
  	var outp = document.getElementById('chars');
  	
    var cnt = getCount();
  	
  	if(cnt > validCharCount)
  	  {
  	  	outp.className = 'charCountGood';
  	  }
  	else
  		{
  			outp.className = 'charCountBad';
  		}
  		
    outp.value = cnt;
  }
	
function sendMail()
  {
  	if(getCount() < validCharCount)
  	  {
  	  	alert('this response is too short');
  	  }
  	else
  		{
  			document.getElementById('theform').submit();
  		}
  }	
	
function loadProfile(pId)
  {
  	window.open('loadProfile.php?profileId=' + pId, 'profile', 'toolbar,menubar,scrollbars,resizable,location,top=20,left=20,width=700,height=600');
  }	
	
</script>

</head>

<body>

Original Message:

<br /><hr noshade width="100%" /></br />

<?php print $text; ?>

<br /><hr noshade width="100%" /></br />

<form id="theform" name="compose_form" method="post" action="sendHostessMail.php?ID=<?php print $senderId; ?>">
	
<input type="hidden" name="ID" value="<?php print $senderId; ?>" />
<input type="hidden" name="action" value="send" />
<input type="hidden" name="textcounter" value="100" />
<input type="hidden" name="sendto" value="both" />
<input type="hidden" name="senderId" value="<?php print $recipientId; ?>" />
<input type="hidden" name="msgId" value="<?php print $msgId; ?>" />

<table><tr>

<!-- compose.php -->

<td align="right">Responding To: </td><td><a href="javascript:loadProfile(<?php print $senderId; ?>);"><?php print $nickName; ?></a></td>

</tr><tr>
	
<td colspan="2" align="right">
	
<input class="charCountBad" id="chars" size="20" />	

</td></tr><tr>	
	
<td colspan="2" align="center">
	
<textarea id="outMsg" name="outMsg" style="width:660px; height: 300px;" onkeydown="trackCharacters(this)"></textarea>	
	
<br />

<input type="button" value="send" onclick="sendMail()" />	
	
</td>
</tr>
</table>

</form>

</body>
</html>