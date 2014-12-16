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
function getWhatIf($ticker, $year, $month, $day, $eYear, $eMonth, $eDay, $numShares)
{
	
	$results = array();

	if(!is_numeric($year) || !is_numeric($month) || !is_numeric($day) || !is_numeric($numShares))
		return -1;

	if($numShares < 0)
		return -5;

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

	$results["ticker"] = $ticker;
	$results["name"] = $result;	
	$results["numShares"] = $numShares;

	//build the two date formats because yahoos two apis dont play together nicely 
	if(ctype_digit($month))
	{
		$month = (int) $month;
		
		if($month < 10)
			$month = "0".$month;
	}

	if(ctype_digit($day))
	{
		$day  = (int) $day; 

		if($day < 10)
			$day = "0".$day;
	}

	if(ctype_digit($eMonth))
	{
		$eMonth = (int) $eMonth;
		
		if($eMonth < 10)
			$eMonth = "0".$eMonth;
	}

	if(ctype_digit($eDay))
	{
		$eDay  = (int) $eDay; 

		if($eDay < 10)
			$eDay = "0".$eDay;
	}

	$date1 = ($year . "-" . $month . "-" . $day);
	$date2 = ($month . "/" . $day . "/" . $year);
	
	$eDate1 = ($eYear . "-" . $eMonth . "-" . $eDay);
	$eDate2 = ($eMonth . "/" . $eDay . "/" . $eYear);


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

	$results["histDate"] = $date1; //using date2 so the two dates are in the same format
	$request->close();

	$results["histPrice"] = $result;
	$results["histTotal"] = $results["histPrice"]  * $results["numShares"];


	//pull current date and format month day if needed
	$today = date("m/d/Y");
	
	//compare to the end date to determin with db to pull from
	if($eDate2 == $today)
	{
		//pull current data and price
		$request = $mysqli->prepare('select last_trade_price, last_trade_date from stocks where symbol=?');
		$request->bind_param("s", $ticker);
		$request->execute();
		$request->bind_result($result1,  $result2);
		$request->fetch();
	
		$request->close();
		$mysqli->close();
	
		$results["endPrice"] = $result1;
		$results["endDate"] = $result2;

		$results["endTotal"] = $results["endPrice"] * $results["numShares"];
		$results["profit"] = $results["endTotal"] - $results["histTotal"];
	}
	
	else
	{
		$request = $mysqli->prepare("select closing_price from history where symbol=? and trade_date=?"); 
		$request->bind_param("ss", $ticker, $eDate1);
		$request->execute();
		$request->bind_result($result);
		$request->fetch();

		if(is_null($result))
		{
			$request->bind_param("ss", $ticker, $eDate2);
			$request->execute();
			$request->bind_result($result);
			$request->fetch();

			if(is_null($result))
			{
				$request->close();
				$mysqli->close();

				return -4;
			}
		}

		$request->close();
	 	$mysqli->close();

		$results["endDate"] = $eDate1;
		$results["endPrice"] = $result;

		$results["endTotal"] = $results["endPrice"] * $results["numShares"];
		$results["profit"] = $results["endTotal"] - $results["histTotal"];

		//$results['graph'] = getWhatIfGraph($ticker, $date1, $eDate1);

	}
	//log what if
	$loggingEngine = new LoggingEngine();
	$loggingEngine->logWhatIfScenario("User ID: ". $uid);

	return $results;
}

/*function getWhatIfGraph($ticker, $start, $end)
{
	//require_once "/home/ssts/simulatedstocktradingsystem/public_html/dist/phpgraphlib/phpgraphlib.php";

	$mysqli = connectDataBase();
	
	$start = strtotime($start);
	$end = strtotime($end);

	echo($start. '</br>');
	echo($end. '</br>');
	echo($ticker. getType($ticker). "</br>");
	
	$request = $mysqli->prepare("select trade_date, closing_price from history where symbol=?");

	echo(getType($request).'</br>');

	$request->bind_param("s", $ticker);
		echo(getType($request).'</br>');

	$request->execute();
		echo(getType($request).'</br>');

	$result = $request->get_result();
	echo(getType($request).'</br>');

	echo(getType($result));
	$count = 0;
	while($row = $result->fetch_assoc())
	{
		$date = strtotime($row['trade_date']);
//		if($date >= $start && $date <= $end)
		{
			$values[] = $row['closing_price'];
			$keys[] = date("Y-m-d", $date);
			$count++;
		}
	}

//	echo(print_r($keys, true));

	$data = array_combine($keys, $values);

	echo(print_r($data, true));
	/*$modValue = $count / 25; //determine how many rows to skip before using a data point so we only have 25.

	if($modValue >= 1)
	{
		foreach($data as $item)
		{
			if((int)($i / $modValue) != 0)
			{
	
			}
		}
	}

	 $graph = new PHPGraphLib(800, 400);
	 $graph->addData($data);
	$graph->setTitle("Historical Prices for");
	 $graph->setBars(false);
	 $graph->setLine(true);
	 $graph->setDataPoints(false);
	// $graph->setDataPointColor('maroon');
	 //$graph->setDataValues(true);
	 //$graph->setDataValueColor('maroon');
	//$graph->createGraph();
	return $graph;

}*/	
