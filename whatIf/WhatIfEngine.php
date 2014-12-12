<?php

	require "/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php";

function connectDataBase()
{
	require "/home/ssts/simulatedstocktradingsystem/public_html/creds.php";

	$mysqli = new mysqli($host, $user, $pass, $db);

	if($mysqli->connect_error)
		die($mysqli->connect_error);

	return $mysqli;
}
/*
	creates and logs a what if scenario

	$ticker - the ticker to preform the scenario on
	$year - the historical year(numeric string or number)
	$month - the historical month (numeric string or number)
	$day - the histocial day (numeric string or number)
	$numShares - the number of shares to be "bought" on the hist date

	returns -1 if the year, month, day, or numShare is not numeric
		-2 the stock ticker does not exist in the stock db
		-3 there is no historical data stored for the input date
		$results An array containing the results of the scenario

	results[] indexes:

		ticker - the stock's ticker symbol
		name - the name of the stock
		numShares - the number of shares to preform scenerio on
		
		histDate - the date to start the scenanrio
		histPrice - the price per share at the histDate
		histTotal - the histPrice * numShares

		currentDate - the date when the what if is submitted
		currentPrice - last trade price 
		currentTotal - currentTotal * numShares

		profit - the currentTotal - histTotal
		
*/
function getWhatIf($ticker, $year, $month, $day, $numShares)
{
	$results = array();

	$results["ticker"] = $ticker;
	$results["numShares"] = $numShares;

	if(!is_numeric($year) || !is_numeric($month) || !is_numeric($day) || !is_numeric($numShares))
		return -1;

	$mysqli = connectDataBase();

	//get stock name fr the ticker
	$request = $mysqli->prepare("select name from stocks where symbol=?");
	$request->bind_param("s", $ticker);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();
	$request->close();

	if(is_null($result))
	{
		$mysqli->close();
		return -2;
	}

	$results["name"] = $result;

	//build the two date formats because yahoos two apis dont play together nicely 
	if(ctype_digit($month))
	{
		$month = (int) $month;
		
		if($month < 10)
			$month = "0".$month;
	}

	if(ctype_digit($day))
	{
		$day  = (int) $month; 

		if($day < 10)
			$day = "0".$day;
	}


	$date1 = ($year . "-" . $month . "-" . $day);
	$date2 = ($month . "/" . $day . "/" . $year);

	echo(($date1) . ($date2));
	//get hist closing price.
	$request = $mysqli->prepare("select closing_price from history where symbol=? and trade_date=?"); 
	$request->bind_param("ss", $ticker, $date1);
	$request->execute();
	$request->bind_result($result);
	$request->fetch();

	if(is_null($result))
	{
		$request->bind_param("ss", $ticker, $date2);
		$request->execute();
		$request->bind_result($result);
		$request->fetch();

		if(is_null($result))
		{
			$request->close();
			$mysqli->close();

			return -3;
		}
	}

	$results["histDate"] = $date2; //using date2 so the two dates are in the same format
	$request->close();

	$results["histPrice"] = $result;
	$results["histTotal"] = $results["histPrice"]  * $results["numShares"];


	//pull current data and price
	$request = $mysqli->prepare('select last_trade_price, last_trade_date from stocks where symbol=?');
	$request->bind_param("s", $ticker);
	$request->execute();
	$request->bind_result($result1,  $result2);
	$request->fetch();
	
	$request->close();
	$mysqli->close();

	$results["currentPrice"] = $result1;
	$results["currentDate"] = $result2;

	$results["currentTotal"] = $results["currentPrice"] * $results["numShares"];
	$results["profit"] = $results["currentTotal"] - $results["histTotal"];

	//log what if
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logWhatIfScenario("User ID: ". $uid);

	return $results;
}
