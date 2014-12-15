<?php
/**
	This file contains methods to allows other parts of the system
	to add and retreive data from the transaction repository
*/

require_once "/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php";
require_once "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";


//because there is already a connectDB in PortfolioEngine it does now need to be recreated here
/*function connectDB()
{
	require "/home/ssts/simulatedstocktradingsystem/public_html/creds.php";
	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_error)
		die($mysqli->connect_error);
	
	return $mysqli;
}*/



/**
	Sells x amount of a stock from an porfolio

	$uid - the user's id
	$portName - the user's portfolio
	$ticker - the stock ticker for which stock to sell.
	$numToSell - the number of shares to sell

	preconditions: $uid and $portName is a valid combo

	returns  1 the transaction was successful
		-1 the number of stocks to sell was below or equal to 0
		-2 the ticker given was invalid
		-3 the portfolio does does not have enough shares to sell
*/
function sellStock($uid, $portName, $ticker, $numToSell)
{
	$result = ""; //holds temp results from all sql queries

	$stockName="";
	$sharePrice = 0;
	$totalCost = 0;
	
	$portCashAfter = 0;
	$portShares = 0;

	if(ctype_digit($numToSell))
		$numToSell = (int)$numToSell;

	if(!is_int($numToSell))
		throw new InvalidArgumentException('$numToSell must be a number');
	if($numToSell <= 0)
		return -1; 

	//pull name and last price of a stock
	$result = getStockNamePrice($ticker);

	if(is_null($result))
	{
		return -2;
	}

	$stockName = $result["name"];
	$sharePrice = $result["last_trade_price"];

	$totalCost = $sharePrice * $numToSell;

	
	
	//get how many shares of the stock is owned by the portfolio
	$portShares = getSingleStock($uid, $portName, $ticker);

	if($portShares < $numToSell)
		return -3;
	
	//if they have enough adjust the number of stocks owned
	$result = removeStockAmount($uid, $portName, $ticker, $numToSell); //will never return an error code becuase those conditions cannot occur in this method

	//chnage the portfolios cash
	adjustPortfolioCash($uid, $portName, $totalCost);

	//store the transaction the dataBase
	insertTransaction($uid, $portName, $ticker, -1 * $numToSell, $sharePrice); 

	//log transaction
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logTransaction("User ID: ".$uid, true, false, $numToSell. "share(s) of " . $stockName. " for ".$totalCost);


	return 1;
}

/**
	Creates and handles a buy transaction for a single portfolio. 

	$uid - the users id
	$portName - the users portfolio name
	$ticker - the ticker for which stock to sell
	$numToBuy - the number of shares to buy

	returns  1 if the transaction was sucessful
		-1 if $numToBuy is <= 0. (no point making a tranaction for 0)
		-2 if the $ticker does not exist in the stock database
		-3 if the portfolio uid combo does not exist
		-4 if the users portfolio does not have enough cash to buy all the shares
*/
function buyStock($uid, $portName, $ticker, $numToBuy)
{
	$stockName = "";
	$stockCost = "";

	$totalCost = 0;
	$portCash = 0;

//	echo("buyStock: passed parms " . $uid . $portName . $ticker . $numToBuy . "</br>");

	if(ctype_digit($numToBuy))
		$numToBuy = (int)$numToBuy;

//	echo("buyStocks: param types " . getType($uid) . " " . getType($portName) . " " . getType($ticker) . " " . getType($numToBuy) . "</br>");

	if(!is_int($numToBuy))
		throw new InvalidArgumentException('$numToBuy must be a number');
	
	if($numToBuy <= 0)
		return -1;

	$result = getStockNamePrice($ticker);

	if(is_null($result))
		return -2;

	$stockName = $result["name"];
	$stockCost = $result["last_trade_price"];

	$totalCost = $numToBuy * $stockCost;


	$portCash = getPortfolioCash($uid, $portName);
//	echo("cash: ".$portCash. "Cost: ". $totalCost."</br>");

	if($portCash == -1) //uid portName combo does not exist
		return -3;

	if($portCash < $totalCost)
		return -4;

	adjustPortfolioCash($uid, $portName, $totalCost *-1);
//	echo("cash adjusted</br>");
	$result = addStockAmount($uid, $portName, $ticker, $numToBuy);
	
//	echo("is anything going to show up?????\n");

//	if(is_null($result))
//		echo("addStockMethod returns null\n");
//	else
//		echo($result."\n");
	insertTransaction($uid, $portName, $ticker, $numToBuy, $stockCost);

	//log
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logTransaction("User ID: ".$uid, true, true, $numToBuy." share(s) of ".$stockName." for ".$totalCost);

	return 1;
}

/**
	gets all of a users transactions from the transactions database

	$uid - the users id
	$portName - The users portfolio.

	returns an array of transaction objects or null if the uid name combo does not exist.
*/
function getAllUserTrans($uid, $portName)
{
	$mysqli = connectDB();

	$transactions = array();
	$counter = 0;

	$request = $mysqli->prepare("select * from transactions where uid=? and name=?");
	$request->bind_param("is", $uid, $portName);
	$request->execute();
	$results = $request->get_result();

	while(!is_null($row = $results->fetch_assoc()))
	{
		$tempTrans = new Transaction($row["ts"], $row["uid"], $row["name"],$row["symbol"], $row["sharePrice"], $row["stocks"], ($row["sharePrice"] * $row["stocks"]));
		$transactions[$counter] = $tempTrans;

		$counter = $counter + 1;

	}

	$request->close();
	$mysqli->close();

	return $transactions;
}

/**
	gets the name and last trade price of a stock ticker

	$ticker - the ticker to check.

	returns an array constaining the name and last price. (column names as index) 
	        null if the ticker is not in the database
*/
function getStockNamePrice($ticker)
{
	$result = array();

	$mysqli = connectDB();

	$request = $mysqli->prepare("select name, last_trade_price from stocks where symbol=?");
	$request->bind_param("s",  $ticker);
	$request->execute();
	$request->bind_result($result1, $result2);
	$request->fetch();
	$request->close();

	$mysqli->close();

	$result["name"] = $result1;
	$result["last_trade_price"] = $result2;

	return $result;
}

/**
	Insets a row into the transaction database.

	$uid - the users id
	$portName - users portfolio name
	$symbol - the stock ticker that is sold/bought
	$shareChange - the change in the number of shares

	Precondition: the uid and portName combo is valid
*/
function insertTransaction($uid, $portName, $symbol, $shareChange, $sharePrice)
{
	$mysqli = connectDB();

	$request = $mysqli->prepare("insert into transactions (uid, name, symbol, stocks, sharePrice) values (?,?,?,?,?)");
	$request->bind_param("issii", $uid, $portName, $symbol, $shareChange, $sharePrice);
	$request->execute();
	$request->close();

	$mysqli->close();
}
