<?php

/**
* Provides methods that allow other parts of the system to get and change data in the portfolio database
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
	Sets a users active portfolio


*/
function setActivePortfolio($uid, $name)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare('update activePortfolio set name=? where uid=?');
	$request->bind_param("si", $name, $uid);
	$request->execute();

	$request->close();
	$mysqli->close();

	return 1;
}

/** 
	Buys a numbers of stock

	$uid - the users id
	$name - name of the portfolio to update
	$symbol - ticker for the stock to be updated
	$num - the ammount of stocks to buy

	returns  0 nothing updated becuase change ammount < 1
		 1 added  the num shares
		 2 added nums shares and added symbol into table
*/
function buyStockAmmount($uid, $name, $symbol, $num)
{
	if(is_float($num) || is_double($num))
		$num = (int)$num;

	if(!is_int($num))
		throw new InvalidArgumentException('$num must be an int');

	if($num < 1)
	{
		return 0;
	}


	$currentTotal = 0;
	$newTotal = 0;

	$mysqli = connectDB();


	//get current shares
	$request = $mysqli->prepare('select stocks from portfolioStocks where uid=? and name=? and symbol=?');
	$request->bind_param("iss", $uid, $name, $symbol);
	$request->execute();
	$request->bind_result($currentTotal);
	$request->fetch();
	$request->close();

	if(is_null($currentTotal)) //if the portfolio does not have the symbol add it
	{
		$request = $mysqli->prepare('insert into portfolioStocks(uid, name, symbol, stocks) values(?, ?, ?, ?)');
		$request->bind_param("issi", $uid, $name, $symbol, $num);
		$request->execute();
		$request->close();

		return 2;
	}

	$newTotal = $currentTotal + $num;

	$request = $mysqli->prepare('update portfolioStocks set stocks=? where uid=? and name=? and symbol=?');
	$request->bind_param("iiss", $newTotal, $uid, $name, $symbol);
	$request->execute();
	$request->close();

	$mysqli->close();

	return 1;
}


/**
	sells a number of stocks owned

	$uid - the users id 
	$name - the portfolio who owns the stocks 
	$symbol - the stock ticker to update
	$num - the ammount of stocks 


	returns -2 if num is less than or equal to 0. you cant sell a negative ammount of stocks
		-1 if the portfolio does not have the symbol.
	         0 no chnage becuase the new total would be below 0
		 1 stock ammount chnaged
		 2 new total was 0 and stock deleted from table
*/
function sellStockAmmount($uid, $name, $symbol, $num)
{
	if(is_float($num) || is_double($num))
		$num = (int)$num;

	if(!is_int($num))
		throw new InvalidArgumentException('$num  must be an int');

	if($num < 1)
		return -2;

	$currentTotal = 0;
	$newNum = 0;

	$mysqli = connectDB();

	//get how many stocks are currently owned by the user.
	$request = $mysqli->prepare('select stocks from portfolioStocks where uid=? and name=? and symbol=?');
	$request->bind_param("iss", $uid, $name, $symbol);
	$request->execute();
	$request->bind_result($currentTotal);
	$request->fetch();
	$request->close();

	if(is_null($currentTotal))
	{
		$mysqli->close();

		return -1;
	}

	$newNum = $currentTotal - $num;

	if($newNum < 0)
	{
		$mysqli->close();

		return 0;
	}

	if($newNum == 0) //if the new total is 0 delete from the table
	{
		$request = $mysqli->prepare('delete from portfolioStocks where uid=? and name=? and symbol=?');
		$request->bind_param('iss', $uid, $name, $symbol);
		$request->execute();
		$request->close();
		$mysqli->close();

		return 2;
	}

	$request = $mysqli->prepare('update portfolioStocks set stocks=? where uid=? and name=? and symbol=?');
	$request->bind_param("diss", $newNum, $uid, $name, $symbol);
	$request->execute();
	
	$request->close();
	$mysqli->close();

	return 1;
}


/**
    Delete a portfolio from the database.

    returns 0 if the portfolio does not exist.
            1 is the portfolio was deleted
*/
function deletePortfolio($uid, $name)
{
	$result = "";

	$mysqli = connectDB();


	//check if a portfolio with that name is owned by the user
	$request = $mysqli->prepare("select name from portfolios where uid=? and name=?");
	$request->bind_param("is", $uid, $name);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();

	if(is_null($result))
	{
		$request->close();
		$mysqli->close();

		return 0;
	}
	
	$request->close();//close the previous request so it doesnt interfere

	$request = $mysqli->prepare("delete from portfolios where uid=? and name=?");
	$request->bind_param("is", $uid, $name);
	$request->execute();

	$request->close();
	$mysqli->close();

	return 1;
}


/**
   Chnages the ammount of cash a portfolio has by the ammount of the paramter $change

   returns -1 if a portfolio with $uid and/or the $name does not exist
           0 if the change ammount would put the total money below 0. (does not chnage the cash value in this case)
	   1 if the cash was changed by the ammount in $change
*/
function adjustPortfolioCash($uid, $name, $change)
{
	if(is_double($change) || is_int($change))
		$change = (float)$change;

	if(!is_float($change))
		throw new InvalidArgumentException("cash change must be floating point");

	$oldCash = 0;
	$newCash = 0;


	$oldCash = getPortfolioCash($uid, $name);

	if(is_null($oldCash))
		return -1;

	$newCash = $oldCash + $change;

	if($newCash < 0)
		return 0;

	setPortfolioCash($uid, $name, $newCash);

	return 1;
}

/**
	sets the ammount of cash a portfolio has.

	return - 0 if the the action failed due to the user not having a portfolio with the supplied name.
	       - 1 if the portfolio's cash was sucessfuly changed.
*/
function setPortfolioCash($uid, $name, $cash)
{
	$result = "";

	$mysqli = connectDB();

	if(is_double($cash)|| is_int($cash))
		$cash = (float)$cash;
	
	if(!is_float($cash))
		throw new InvalidArgumentException("Cash must be floating point");

	//check that the user has a portfolio with that name
	$request = $mysqli->prepare("select name from portfolios where uid=? and name=?");
	$request->bind_param("is", $uid, $name);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();

	if(is_null($result))
	{
		$mysqli->close();
		$request->close();
		return 0;
	}

	
	$request->close();
	$request = $mysqli->prepare("update portfolios set cash=? where uid=? and name=?");
	$request->bind_param("dis", $cash, $uid, $name);
	$request->execute();
	
	$request->close();
	$mysqli->close();

	return 1;
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

	return true;
}





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
function getPortfolioCash($uid, $portName)
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

