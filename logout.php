<?php

session_start();

// Unset all of the session variables.
$_SESSION = array();

if(isset($_COOKIE[session_name()])) 
  {
    setcookie(session_name(), '', time()-42000, '/');
  }

// Finally, destroy the session.
session_destroy();

// back to index
header("Location: index.php");

?>