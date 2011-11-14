<?php

include("../includes/variables.php");

$emailFlag = 'html';
$subject = 'You have a new message!';

$insId = null;
$messageText = $_POST['outMsg'];
$hostess_id = $_POST['senderId'];
$recipient_id = $_POST['ID'];

$qr = mysql_query("SELECT * FROM `profiles` WHERE `id` = $recipient_id");
$qs = mysql_query("SELECT * FROM `profiles` WHERE `id` = $hostess_id");

$recipient = mysql_fetch_array($qr);
$sender = mysql_fetch_array($qs);

$member['id'] = $recipient_id;
$member['password'] = $recipient['password'];

// Perform sending
$insId = sendMessage($sender,$recipient);

if(!$insId)
  {
  	print "There was an error in sending this mail.";
  }
else
  {
		@session_start();
		
		$moderatorId 	= $_SESSION['moderatorId'];
		
    $origMsgId = $_POST['msgId'];
    
		// update Messages (flag send)
		mysql_query("UPDATE messages SET new = '0' WHERE id = $origMsgId");
		    
		// now get userId
		$q = mysql_query("SELECT sender,recipient FROM messages WHERE id = $origMsgId");
		$u = mysql_fetch_array($q);
		$hostessId = $u['recipient'];
		$senderId = $u['sender'];
		    
		// update hostess_history
		$aq = "INSERT INTO hostess_history (messageId,responseId,hostessId,moderatorId) VALUES ($origMsgId,$insId,$hostessId,$moderatorId)";

		mysql_query($aq);
		    
		print '<script type="text/javascript">opener.loadUserHistory('.$hostessId.','.$origMsgId.','.$insId.');opener.closeComposeDialog();</script>';
  }

/**
 * Send message
 */
function sendMessage($hostess, $recipient)
  {
    global $emailFlag;		
    global $subject;
    global $messageText;
    
    $hostessId = $hostess['id'];
    $recipientId = $recipient['id'];
  		
  	if($emailFlag == 'html')
    	{
    		$headers = "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=iso-8859-1\r\n" . "From: {yoursite.com} <yoursite@yoursite.com>";
    		$result = mail($recipient['email'], $subject, $messageText, $headers);
    	}
    else
    	{
    		$result = mail($recipient['email'], $subject, html2txt($messageText), "From: yoursite@yoursite.com");
    	}
  
  	// Insert message into database
  	$q = "INSERT INTO messages (date,sender,recipient,text,new) VALUES (NOW(),$hostessId,$recipientId,'$messageText','1')";
  	
    mysql_query($q);
    
    if($result)
      {
        return mysql_insert_id();
      }
    else
      {
  	    return false;
  	  }
  }
?>