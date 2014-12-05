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
/*
	Get a user's active portfolio

	returns a sting with the name of the uid's active portfolio or null if that uid does not have an active portfolio
*/
function getActivePortfolio($uid) 
{
  $mysqli = connectDB();
  $request = $mysqli->prepare('select name from activePortfolio
    where uid=?');
  $request->bind_param('i', $uid);
  $request->execute();
  $request->bind_result($activePortfolio);
  $request->fetch();
  $request->close();
  $mysqli->close();
  return $activePortfolio;

}


/**
	Sets a users active portfolio

	returns 1 if the active portfolio was set
*/
function setActivePortfolio($uid, $name)
{
	$mysqli = connectDB();
	$testResult = "";

	//check that the uid is in the tbale
	$request = $mysqli->prepare('select uid from activePortfolio where uid=?');
	$request->bind_param("i", $uid);
	$request->execute();
	$request->bind_result($testResults);
	$request->fetch();
	$request->close();

	if(is_null($testResults))
	{
		$mysqli->close();

		return 0;
	}

	//if the uid is in the table set the active portfolio
	$request = $mysqli->prepare('update activePortfolio set name=? where uid=?');
	$request->bind_param("si", $name, $uid);
	$request->execute();

	$request->close();
	$mysqli->close();

	//log change in active portfolio
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logActivePortSet("User ID: " . $uid, $name);

	return 1;
}

/** 
	adds a numbers of stock

	$uid - the users id
	$name - name of the portfolio to update
	$symbol - ticker for the stock to be updated
	$num - the ammount of stocks to buy

	returns -1 if the ticker does not exist in the stock database
		 0 nothing updated becuase change ammount < 1
		 1 added  the num shares
		 2 added nums shares and added symbol into table
*/
function addStockAmount($uid, $name, $symbol, $num)
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
	
	
	//check that the symbol is valid
	$request = $mysqli->prepare("select symbol from stocks where symbol=?");
	$request->bind_param("s", $symbol);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();

	if(is_null($result))
		return -1;
	
	
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

		$loggingEngine = new LoggingEngine();
		$loggingEngine->logStockShareChange("User ID : ".$uid, $name, $symbol, 0, $num);

		return 2;
	}

	$newTotal = $currentTotal + $num;

	$request = $mysqli->prepare('update portfolioStocks set stocks=? where uid=? and name=? and symbol=?');
	$request->bind_param("iiss", $newTotal, $uid, $name, $symbol);
	$request->execute();
	$request->close();

	$mysqli->close();

	//log stock amount change
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logStockShareChange("User ID: ".$uid, $name, $symbol, $currentTotal, $newTotal);

	return 1;
}


/**
	removes a number of stocks owned

	$uid - the users id 
	$name - the portfolio who owns the stocks 
	$symbol - the stock ticker to update
	$num - the ammount of stocks 


	returns -3 if the stock ticker does not exist in the database
		-2 if num is less than or equal to 0. you cant sell a negative ammount of stocks
		-1 if the portfolio does not have the symbol.
	         0 no chnage becuase the new total would be below 0
		 1 stock ammount chnaged
		 2 new total was 0 and stock deleted from table
*/
function removeStockAmount($uid, $name, $symbol, $num)
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

	
	//check that the symbol is valid
	$request = $mysqli->prepare("select symbol from stocks where symbol=?");
	$request->bind_param("s", $symbol);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();

	if(is_null($result))
		return -3;
	
	
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

		$loggingEngine = new LoggingEngine();
		$loggingEngine->logStockShareChange("User ID: ".$uid, $name, $symbol, $currentTotal, $newNum);

		return 2;
	}

	$request = $mysqli->prepare('update portfolioStocks set stocks=? where uid=? and name=? and symbol=?');
	$request->bind_param("diss", $newNum, $uid, $name, $symbol);
	$request->execute();
	
	$request->close();
	$mysqli->close();

	//log share change
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logStockShareChange("User ID: ".$uid, $name, $symbol, $currentTotal, $newNum);
	
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

	//log deletion
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logPortDeletion("User ID: ".$uid, $name);

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

	setPortfolioCash($uid, $name, $oldCash, $newCash);

	return 1;
}

/**
	sets the ammount of cash a portfolio has.

	return - 0 if the the action failed due to the user not having a portfolio with the supplied name.
	       - 1 if the portfolio's cash was sucessfuly changed.
*/
function setPortfolioCash($uid, $name, $oldCash, $cash)
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

	//log cash change
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logCashChange("User ID: ".$uid, $name, $oldCash, $cash);

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

	$result = "";

	//check if the user already has that name
	//dont need another prepare line becuase it will the same as the last request
	$request->bind_param("is", $uid, $newName); 
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();
	
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

	//log port rename
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logPortRenamed("User ID: ".$uid, $oldName, $newName);

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

    $returns - A 2D array. Each element of the array is
    an array containing the portfolio name and its cash.
*/

function getUserPortfolios($uid)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare("select name, cash
	  from portfolios where uid=?");
	$request->bind_param("i", $uid);
	$request->execute();
	$request->bind_result($result[0], $result[1]);

 	$count = 0;
	while($request->fetch())
	{
		$portfolioArray[$count] = array($result[0], $result[1]);
		$count++;
	}

	//free system resources
	$request->close();
	$mysqli->close();

	return $portfolioArray;
}

/**
    Given a user Id this method will pull all the inactive portfolios which belong to this user.

    $uid - the id of the user

    $returns - A 2D array. Each element of the array is
    an array containing the portfolio name and its cash.
*/

function getInactiveUserPortfolios($uid)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare("select portfolios.name, cash
	  from portfolios, activePortfolio where 
	  portfolios.uid = activePortfolio.uid
	  and portfolios.uid=?
	  and activePortfolio.name != portfolios.name");
	$request->bind_param("i", $uid);
	$request->execute();
	$request->bind_result($result[0], $result[1]);

 	$count = 0;
	while($request->fetch())
	{
		$portfolioArray[$count] = array($result[0], $result[1]);
		$count++;
	}

	//free system resources
	$request->close();
	$mysqli->close();

	return $portfolioArray;
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
		$ownedSymbols[$counter] = $tempSymbol;
		$counter = $counter + 1;
	}
	$request->close();

	//if there are no stocks owned by that portfolio
	if(count($ownedSymbols) == 0)
		return 0;

	//pull the names of the stocks from the stock database
	$request = $mysqli->prepare("select name from stocks where symbol=?");

	for($i = 0; $i < $counter; $i++) 
	{
		$request->bind_param("s", $ownedSymbols[$i]);
		$request->execute();
		$request->bind_result($tempName);
		$request->fetch();

		$stockNames[$i] = $tempName;
	}
	$request->close();
	

	//use the pulled symbols to pull the number of stocks owned 
	$request = $mysqli->prepare("select stocks from portfolioStocks where uid=? and name=? and symbol=?");
	
	for($i = 0; $i < $counter; $i++)
	{
		$request->bind_param("iss", $uid, $name, $ownedSymbols[$i]);
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

    returns - the cash ammount of the portfolio or -1 if the portfolio uid combo does not exist

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

	if(is_null($cash))
	{
		$request->close();
		$mysqli->close();

		return -1;
	}
	
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

/*
  input: user's id

  output: an associative array with all the transactions associated
  with the given id

  WARNING: INSECURE. DO NOT USE USER INPUT
*/

function getTransactions ($uid) {

  $mysqli = connectDB();
  
  $result = $mysqli->query("select ts, name, symbol, stocks,
    sharePrice from transactions where uid=$uid");
    
  $count=0;
  while($row = $result->fetch_assoc()) {
    $transaction[$count] = array(
      "ts" => $row["ts"], 
      "name" => $row["name"],
      "symbol" => $row["symbol"], 
      "stocks" => $row["stocks"], 
      "sharePrice" => $row["sharePrice"]
    );
    $count++;
  } 
  
  $mysqli->close();

  return $transaction;

}

?>
}
