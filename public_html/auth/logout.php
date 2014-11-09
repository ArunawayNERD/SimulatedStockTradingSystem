<?php
  session_start();
  // logs that a user has logged out
  include 
    '/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php';
  $log=new LoggingEngine();
  $log->logUserLogout($_SESSION['username']);
  
  $_SESSION = array();
  setcookie(session_name(), '', time() - 2592000, '/');
  session_destroy();
  
  header("Location: http://pluto.hood.edu/~ssts/");

?>
