<?php

require_once("includes/variables.php");

$username = $_POST['username'];
$password = $_POST['password'];
$email = $_POST['email'];

if($username && $password && $email)
  { 
    $q = "INSERT INTO moderators (username,password,email,last_login) values ('$username','$password','$email',NOW())";
    $r = @mysql_query($q);
    
    if(!$r)
      {
        print "Account creation failed.  Given mysql error: ".mysql_error();
        exit; 
      } 
    
    header("Location: addModerator.php");
    
  }
  
print "Information missing.";

?>