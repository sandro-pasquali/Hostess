<?php

require_once("includes/variables.php");
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
<meta name="Copyright" content="Copyright (c) Unified Applications Inc." />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="MSSmartTagsPreventParsing" content="true" />

<script type="text/javascript"></script>

</head>

<body>
	
<br />

<table cellpadding="4" cellspacing="0" border="0"><tr>
<form method="post" action="writeNewModerator.php">

<td>Username:</td>
<td><input type="text" name="username" value="" size="20" maxlength="32" /></td>
</tr>

<tr>
<td>Password:</td>
<td><input type="password" name="password" value="" size="20" maxlength="32" /></td>
</tr>

<tr>
<td>Email:</td>
<td><input type="text" name="email" value="" size="20" maxlength="32" /></td>
</tr>

<tr><td colspan="2">
  
<input type="submit" value="Add Moderator" />

</form>

</body>
</html>
