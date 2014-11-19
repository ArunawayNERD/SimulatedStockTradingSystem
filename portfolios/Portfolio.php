<?php

/**
* implements a portfolio objet which holds values for cash, stock, etc
* created by John Pigott
*/

require_once '/home/ssts/simulatedstocktradingsystem/portfolios/Stocks.php';

function connectPortDB()
{
	require_once '/home/ssts/simulatedstocktradingsystem/public_html/creds.php';
	
	$portDB = new mysqli($host, $user, $pass, $db);

	if($portDB->connect_error)
		die($portDB->connect_error);

	return $portDB;
}

function getUserPortfolios($user)
{
	$portfolioNames = array();

	$portDB = connectPortDB();

	$request = $portDB->query("select name from portfolios where uid=" . $user );

 	$count = 0;
	while($row = $request->fetch_assoc())
	{
		$portfolioNames[$count] = $row["name"];
		$count = $count + 1;	
	}


	return $portfolioNames;
}

function getUserOwner()
{
	return (String) $this->userOwner;
}

/**
* Returns the stocks that portfolio has. 
* @param returnArray - 1 if you want an array of stock objects.
		       0 if you want a formated string of stock objects
*/
function getStocks($returnArray)
{
	if($returnType == 1)
	{
		return $this->stocks;
	}

	$stockString = "";

	foreach($stocks as $singleStock)
	{
		$stockString = $stockString . $singleStock->toString();
	}
	return $stockString;
}

function getCash()
{
	return (double) $this->cash;
}

function getTransactions()
{
	return $this->transactions;
}

function getActiveState()
{
	return $this->active;
}

function getCompetetionState()
{
	return $this->competition;
}

