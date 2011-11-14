<?php

require_once("../includes/variables.php");

$Recipient 	= $_POST['recipientId'];
$Sender 		= $_POST['senderId'];
$text				= addslashes($_POST['text']);
$msgId			= $_POST['msgId'];

mysql_query("INSERT INTO messages (date,sender,recipient,text) values (now(),$Sender,$Recipient,'$text')");

$insertId = mysql_insert_id();


?>

<script type="text/javascript">
	//this.opener.document.reloadMenu();
	
	alert(this.opener.reloadMenu);
</script>