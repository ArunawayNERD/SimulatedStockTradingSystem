<?php

/**
* implements a portfolio objet which holds values for cash, stock, etc
* created by John Pigott
*/

require_once '/home/ssts/simulatedstocktradingsystem/portfolios/Stocks.php';

function connectDB()
{
	require '/home/ssts/simulatedstocktradingsystem/public_html/creds.php';
	
	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_error)
		die($mysqli->connect_error);

	return $mysqli;
}

function getUserPortfolios($uid)
{
	$portfolioNames = array();

	$mysqli = connectDB();

	$request = $mysqli->query("select name from portfolios where uid=" . $uid);

 	$count = 0;
	while($row = $request->fetch_assoc())
	{
		$portfolioNames[$count] = $row["name"];
		$count = $count + 1;	
	}

	//free system resources
	$request->free();
	$mysqli->close();

	return $portfolioNames;
}

/*
  Dont think this would work becuase differnt users can have the same portfolio names. 
  and if the ui doesnt alrealy know the user name/id then there would be no way for the 
  us to figure out which result to use
function getUserOwner($portName)
{
	$userID = 0;
	$userName = "";

	$mysqli = connectDB();

	
	//get user id
	$request = $mysqli->prepare("select uid from portfolios where name=?");
	$request->bind_param("s", $portName);
	$request->execute();

	$request->bind_result($userID);
	$request->fetch();


	return $userID;
}*/

/**
* Returns the stocks that portfolio has. 
* @param returnArray - 1 if you want an array of stock objects.
		       0 if you want a formated string of stock objects
*/
function getStocks($returnArray)
{
	if($returnType == 1)
	{
	
	}

	$stockString = "";

	foreach($stocks as $singleStock)
	{
		$stockString = $stockString . $singleStock->toString();
	}
	return $stockString;
}

/**
    Pulls a portfolios current cash ammount from the portfolio database.
    
    $uid - The user id of the user who owns the portfolio
    $portName - the name of the portfolio from which the cash ammount is pulled

    returns - the cash ammount of the portfolio

*/
function getCash($uid, $portName)
{
	$cash = 0;

	$mysqli = connectDB();

	$request = $mysqli->prepare("select cash from portfolios where uid=? and name=?");
	$request->bind_param('is', $uid, $portName);
	$request->execute();
	$request->bind_result($cash);
	$request->fetch();


	//free system resources
	$request->close();
	$mysqli->close();

	return $cash;
}

function getTransactions()
{
	return $this->transactions;
}

function getCompetetionState()
{
	return $this->competition;
}

