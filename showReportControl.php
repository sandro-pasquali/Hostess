<?php

$startDate =  isset($_POST['startDate']) ? $_POST['startDate'] : date("Y-m-d 00:00:00",(time() - 60*60*24*30));
$endDate =  isset($_POST['endDate']) ? $_POST['endDate'] : date("Y-m-d 23:00:00",time());

?>

<form name="datePicker" action="showModeratorReport.php" method="post">
			
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="startDate">Start Date: </label><input type="Text" name="startDate" value="<?php print $startDate; ?>"><a href="javascript:startingDate.popup();"><img src="images/cal.gif" width="16" height="16" border="0" alt="click for start date"></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp <label for="endDate">End Date: </label><input type="Text" name="endDate" value="<?php print $endDate; ?>"><a href="javascript:endingDate.popup();"><img src="images/cal.gif" width="16" height="16" border="0" alt="click for end date"></a>
				
<br /><br />				
				

<input type="submit" value="get moderator report" />



				
</form>
