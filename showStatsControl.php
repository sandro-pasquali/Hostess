<form id="statsform" name="datePicker" action="" method="post">
	
<?php

@session_start();

$startDate =  isset($_POST['startDate']) ? $_POST['startDate'] : date("Y-m-d 00:00:00",(time() - 60*60*24*30));
$endDate =  isset($_POST['endDate']) ? $_POST['endDate'] : date("Y-m-d 23:00:00",time());

/*
 * get moderator info.
 *
 * NOTE that this file can be called either by
 * an admin, or by an individual moderator.  admins
 * will be able to view ANY moderator stats; moderators
 * can see only their own.  so check if this user
 * has admin priviledges; if so, see all. if not,
 * only own stats (moderatorID, moderatorName).
 *
 * see also showStatsControl.php
 */
if(isset($_POST['moderator']))
  {
    $mInf = explode("::",$_POST['moderator']);
    $moderatorId = $_SESSION['lastModeratorId'] = $mInf[0];
    $moderatorName = $_SESSION['lastModeratorName'] = $mInf[1];
  }

// only admins get moderator list
if(isset($_SESSION['adminOK']) && ($_SESSION['adminOK'] == "confirmed"))
  {
		print '<label for="selectModerator">Select Moderator: </label><select name="moderator">';
		$mQ = "SELECT id,username FROM moderators where status = 1";
		$mR = mysql_query($mQ);
		
		while($mx = mysql_fetch_array($mR))
		  {
		  	$sel = ($mx['id'] == $_SESSION['lastModeratorId']) ? ' selected ' : '';
		  	print '<option value="'.$mx['id'].'::'.$mx['username'].'"'.$sel.'>'.$mx['username'].'</option>';
		  }
		  
		print '</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  }
else
  {
		$moderatorId = $_SESSION['moderatorId'];
		$moderatorName = $_SESSION['moderatorName'];
		
  	// an individual moderator only gets own stats
  	print '<input type="hidden" value="'.$moderatorId.'::'.$moderatorName.'" name="moderator">';
  }
?>
	
<label for="startDate">Start Date: </label><input type="Text" name="startDate" value="<?php print $startDate; ?>"><a href="javascript:startingDate.popup();"><img src="images/cal.gif" width="16" height="16" border="0" alt="click for start date"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp <label for="endDate">End Date: </label><input type="Text" name="endDate" value="<?php print $endDate; ?>"><a href="javascript:endingDate.popup();"><img src="images/cal.gif" width="16" height="16" border="0" alt="click for end date"></a>
				
<br /><br />				
				

<input type="button" onclick="showStats('recipient')" value="get stats by recipient" /> <input type="button" onclick="showStats('sender')" value="get stats by sender" /> 

<?php

/*
 * admins checking a selected moderator's stats will want
 * to return to that selected moderator's stats. admins are particular
 * in that they will not have any session var for moderatorName when 
 * this report is first loaded -- only gets set once admin requests
 * a specific moderator's report So, only give button if moderatorName
 * is set.
 */

if((isset($_SESSION['adminOK']) && ($_SESSION['adminOK'] == "confirmed")) && $moderatorName)
  {
    print '<input type="button" onclick="top.location.href = \'loadInbox.php\';" value="view inbox for '.$moderatorName.'">';
  }

?>
				
</form>
