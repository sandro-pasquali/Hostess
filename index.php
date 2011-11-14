<?php 
print "hostess";
exit;
require_once("hostessAdminOptions.php");

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
  
</style>

<script type="text/javascript"></script>

</head>

<body>

<form action="loadInbox.php" method="post">

<fieldset>
  <legend>MODERATOR LOGIN</legend>	

	<table cellpadding="6"><tr><td align="right">
	<label for="username">Moderator Login Name: </label>
	
	</td><td><select name="moderatorId">
	
	
<?php

require_once("includes/variables.php");


$q = "SELECT id,username FROM moderators where status = 1";

$m = mysql_query($q);

while($r = mysql_fetch_assoc($m))
  {
  	$mID = $r['id'];
  	$mName = $r['username'];
  	
  	print '<option value="'.$mID.'">'.$mName.'</option>';
  	
  }

?>	
	
	
</select></td></tr>

	<tr><td align="right"><label for="password">Password: </label></td><td><INPUT size="20" type="password" maxlength="30"	name="password" value=""></td></tr>
	
  <tr><td colspan="2">
  
  <input type="hidden" name="showHistory" value="on" />
  <input type="submit" value="start" />
  
</td></tr></table>

</fieldset>

</form>

</body>
</html>
