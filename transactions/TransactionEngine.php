<?php
/**
	This file contains methods to allows other parts of the system
	to add and retreive data from the transaction repository
*/

require "/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php";

function connectDB()
{
	require "/home/ssts/simulatedstocktradingsystem/public_html/creds.php";
	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_error)
		die($mysqli->connect_error);
	
	return $mysqli;
}

function getAllUserTrans($uid, )
