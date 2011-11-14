<?php
session_start();

$matchAU = 'admin';
$matchAP = 'admin';

$AU = isset($_POST['adminUsername']) ? $_POST['adminUsername'] : '';
$AP = isset($_POST['adminPassword']) ? $_POST['adminPassword'] : '';

if(($AU == $matchAU) && ($AP == $matchAP))
  {
    $_SESSION['adminOK'] = "confirmed";
  }

?>

<fieldset style="background-color:#F8F8F8;">
	
  <legend>ADMIN FUNCTIONS</legend>	
  
<?php

if(!isset($_SESSION['adminOK']) || ($_SESSION['adminOK'] != "confirmed"))
  {
?>

<form method="POST" action="<?php print $_SERVER['PHP_SELF']; ?>">
	
<table cellpadding="4"><tr>
<td align="right">

<label for="adminUsername">Admin Login Name:</label>
	
</td><td><INPUT size="20" type="text" maxlength="20" name="adminUsername" value="" /></td></tr>

<tr><td align="right"><label for="adminPassword">Password: </label></td><td><INPUT size="20" type="password" maxlength="30"	name="adminPassword" value=""></td></tr>

  <tr><td colspan="2">
  	
  <input type="submit" value="login" />
  
</td></tr></table>

</form>

<?php
  
    // don't show any more, unless index page, where other logins exist, or moderator stats page
    
    if(($_SERVER['PHP_SELF'] != '/hostess/index.php') && ($_SERVER['PHP_SELF'] != '/hostess/showModeratorStats.php') && ($_SERVER['PHP_SELF'] != '/hostess/showSenderStats.php'))
      {
      	exit;
      }
  }
else
  {

?>

<a style="float: left; padding: 6px; border: 1px dashed silver; margin: 6px; color: #000000; font-family: Verdana; font-size: 11px; font-weight: bold; text-decoration: none;" href="showModeratorReport.php">MODERATOR REPORTS</a><a style="float: left; padding: 6px; border: 1px dashed silver; margin: 6px; color: #000000; font-family: Verdana; font-size: 11px; font-weight: bold; text-decoration: none;" href="doHostessAssignments.php">ASSIGN HOSTESSES</a><a style="float: left; padding: 6px; border: 1px dashed silver; margin: 6px; color: #000000; font-family: Verdana; font-size: 11px; font-weight: bold; text-decoration: none;" href="addModerator.php">ADD MODERATOR</a><a style="float: left; padding: 6px; border: 1px dashed silver; margin: 6px; color: #000000; font-family: Verdana; font-size: 11px; font-weight: bold; text-decoration: none;" href="setPayouts.php">SET MODERATOR PAYOUTS</a><a style="float: left; padding: 6px; border: 1px dashed silver; margin: 6px; color: #000000; font-family: Verdana; font-size: 11px; font-weight: bold; text-decoration: none;" href="index.php">HOME</a>
<br clear="left" /><br />

<form action="logout.php" method="post">
	
<input type="submit" value="log out" />	
	
</form>  

<?php

  }

?>
  
</fieldset>