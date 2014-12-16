<?php
	require "/home/ssts/simulatedstocktradingsystem/public_html/dist/phpgraphlib/phpgraphlib.php";

//	getWhatIfGraph($_GET['ticker'], $_GET['start'], $_GET['end']); 

//function getWhatIfGraph($ticker, $start, $end)
//{
	 require '/home/ssts/simulatedstocktradingsystem/public_html/creds.php';

	 $mysqli = new mysqli($host, $user, $pass, $db);

	 if($mysqli->connect_error){
	 	echo "Failed to connect to MySQL: " . mysqli_connect_error();
		 die($mysqli->connect_error);
	 }
									
	 $ticker = $_GET['ticker'];


	$result = $mysqli->query("select * from
	  (select trade_date, closing_price 
	  from history
	  where symbol=\"$ticker\" 
	  order by trade_date desc
	  limit 100)inside order by trade_date asc;");
        
	 while ($row = $result->fetch_assoc()) {
           $keys[]=$row["trade_date"];
	   $values[]=$row["closing_price"];
	   //echo $row["trade_date"];
	   //echo $row["closing_price"];
	 }
         
	 $data = array_combine($keys, $values);
	//echo print_r($data, true);

        $graph = new PHPGraphLib(500, 300);
        $graph->addData($data);
        $graph->setTitle("Prices for ". $_GET['ticker']);
        $graph->setBars(false);
        $graph->setLine(true);
	$graph->setLineColor("red");
        $graph->setDataPoints(false);

	$graph->setXValuesInterval(5);
       
       $graph->setRange((int)max($values) + 5, (int)min($values) - 5);
       $graph->setDataCurrency("dollar");
       $graph->createGraph();
?>
