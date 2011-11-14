<?php

$MODERATOR_FILEPATH = '/path/to/hostess/moderators/';
$ALLOW_MULTIPLE_RESPONSES = false;

$DB_LOCATION = "localhost";
$DB_USERNAME = "root";
$DB_PASSWORD = "dbass";
$DB_NAME = "HOSTESS";
		    
$DB = @mysql_connect($DB_LOCATION, $DB_USERNAME, $DB_PASSWORD);
@mysql_select_db($DB_NAME,$DB);

?>