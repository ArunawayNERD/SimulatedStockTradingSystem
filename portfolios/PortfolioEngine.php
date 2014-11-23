<?php

/**
* implements a portfolio objet which holds values for cash, stock, etc
* created by John Pigott
*/

require_once "/home/ssts/simulatedstocktradingsystem/portfolios/Stock.php";
require_once "/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php";

/**
    Connects to the ssts database and returns a mysqli object.
*/
function connectDB()
{
	require '/home/ssts/simulatedstocktradingsystem/public_html/creds.php';
	
	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_error)
		die($mysqli->connect_error);

	return $mysqli;
}

/**
	sets the ammount of cash a portfolio has.

	return
*/
function setPortfolioCash($uid, $name, $cash)
{
	$result = "";

	$mysqli = connectDB();

	if(!is_float($cash))
	{

	}


	$request = $mysqli->prepare("select name from portfolios where uid=? and name=?");
	$request->bind_param("is", $uid, $name);
	$request->execute();
	$request->bind_result($result


}

/**
    Set changes the name of the portfolio

    returns - -1 if the name was not changed becuase the user does not have a portfolio with the old name
    	    - 0 if the name was not changed due to a user already having a portfolio with that name.
    	    - 1 if the name was chnaged
*/

function changePortfolioName($uid, $oldName, $newName)
{
	$result = "";

	$mysqli = connectDB();


	//check that the user has a portfolio with the old name
	$request = $mysqli->prepare("select name from portfolios where uid=? and name=?");
	$request->bind_param("is", $uid, $oldName);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();

	if(is_null($result))
	{
		$request->close();
		$mysqli->close();
		return -1;
	}

	//check if the user already has that name
	//dont need another prepare line becuase it will the same as the last request
	$request->bind_param("is", $uid, $newName); 
	$request->execute();
	$request->bind_result($result);
	$request->fetch();

	if(!is_null($result))
	{
		$request->close();
		$mysqli->close();
		return 0;
	}
	

	//update the name
	$request = $mysqli->prepare("update portfolios set name=? where uid=? and name=?");
	$request->bind_param("sis", $newName, $uid, $oldName);
	$request->execute();

	//free system resources
	$request->close();
	$mysqli->close();

	return 1;
}

/**
    Creat a new portfolio and add it to the database

    returns - false if the user already has a portfolio with that name. 
            - true if the portoflio was sucessfuly created. 
*/
function makeNewPortfolio($uid, $name)
{
	$result="";
	
	$mysqli = connectDB();
	
	//check if the portfolio name is taken already
	$request = $mysqli->prepare("select name from portfolios where uid=? and name=?");
	$request->bind_param("is", $uid, $name);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();

	if(!is_null($result))
	{
		$request->close();
		$mysqli->close();
		return false;
	}


	// if the name is not already in use then add the new portfolio.
	$request = $mysqli->prepare("INSERT INTO portfolios (uid, name, cash, competition) VALUES (?, ?, 100000, 0)");
	$request->bind_param("is", $uid, $name);
	$request->execute();

	//free system resources
	$request->close();
	$mysqli->close();

	//log portfolio creation.
	$logger = new LoggingEngine();
	$logger->logPortCreation("User ID: " . $uid);
		
}	return true;





/**
    Given a user Id this method will pull all the portfolio names which belong to this user.

    $uid - the id of the user

    $returns - an array of strings with all the portfolio names.
*/

function getUserPortfolios($uid)
{
	$portfolioNames = array();
	$singleName = "";

	$mysqli = connectDB();


	$request = $mysqli->prepare("select name from portfolios where uid=?");
	$request->bind_param("i", $uid);
	$request->execute();
	$request->bind_result($singleName);

 	$count = 0;
	while($request->fetch())
	{
		$portfolioNames[$count] = $singleName;
		$count++;
	}

	//free system resources
	$request->close();
	$mysqli->close();

	return $portfolioNames;
}


/**
    Gets a single stockowned by the portfolio from the database

*/
function getSingleStock($uid, $name, $symbol)
{
	$stockNum = 0;
	$stockName = "";
	$mysqli = connectDB();

	$request = $mysqli->prepare("select stocks from portfolioStocks where uid=? and name=? and symbol=?");
	$request->bind_param("iss", $uid, $name, $symbol);
	$request->execute();
	$request->bind_result($stockNum);
	$request->fetch();

	if(is_null($stockNum))
		$stockNum = 0;
	
	return $stockNum;
}

/**
* Returns all the stocks that a portfolio has. 
* 
* returns an array of stock objects
*/
function getAllStocks($uid, $name)
{
	$ownedSymbols = array();
	$tempSymbol = "";
	
	$ownedStocks = array(); //will hold stock objects which old the name and the ammount;
	$tempStocks = 0;

	$stockNames =array();
	$tempName = "";

	$counter = 0;

	$mysqli = connectDB();

	//pull all the symbols the portfolio owns
	$request = $mysqli->prepare("select symbol from portfolioStocks where uid=? and name=?");
	$request->bind_param("is", $uid, $name);
	$request->execute();
	$request->bind_result($tempSymbol);

	while($request->fetch())
	{
		$ownedStocks[$counter] = $tempSymbol;
		$counter = $counter + 1;
	}

	//if there are no stocks owned by that portfolio
	if(count($ownedSymbols) == 0)
		return 0;

	//pull the names of the stocks from the stock database
	$request = $mysqli->prepare("select name from stocks where symbol=?");

	for($i = 0; $i < $counter + 1; $i++) 
	{
		$request->bind_param("s", $ownedSymbols[$i]);
		$request->execute();
		$request->bind_result($tempName);
		$request->fetch();

		$stockNames[$i] = $tempName;
	}
	
	
	//use the pulled symbols to pull the number of stocks owned 
	$request = $mysqli->prepare("select stocks from portfolioStocks where uid=? and name=? and symbol=?");
	
	for($i = 0; $i < $counter + 1; $i++)
	{
		$request->bind_param("iss", $uid, $name, $onwedSymbols[$i]);
		$request->execute();
		$request->bind_result($tempStocks);
		$request->fetch();

		$ownedStocks[$i] = new Stock($ownedSymbols[$i],$stockNames[$i], $tempStocks);
	}

	
	return $ownedStocks;
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


/**
   Pulls the competition status for  given 
   portfoliofrom the portfolio data base

   $uid - the owner's uid
   $name - name of the portfilio from which the status should be pulled
 
   returns - the competition status of the portfolio 
*/
function getCompetitionState($uid, $name)
{
	$compStatus = 0;

	$mysqli = connectDB();

	$request = $mysqli->prepare("select competition from portfolios where uid=? and name=?");
	$request->bind_param("is", $uid, $name);
	$request->execute();
	$request->bind_result($compStatus);
	$request->fetch();

	//free system rresources
	$request->close();
	$mysqli->close();
	
	return $compStatus;
}

