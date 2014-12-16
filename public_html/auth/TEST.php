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
									
         //$start = "2005-08-12";  //$_GET['end'];
         $start = "2008-01-12";  //$_GET['end'];
         $end = "2008-04-01"; //$_GET['start']; 
	 $ticker = "IBM";  //$_GET['ticker'];

         // count the number of results

	$result = $mysqli->query("select count(*) from history
	  where symbol=\"$ticker\" and 
	  trade_date between \"$start\" and \"$end\";");

         $row = $result->fetch_assoc();
         $numDates = $row["count(*)"];
         //echo $numDates . "\t";
         
	 if($numDates > 100) {
           $gap = (int)($numDates/100);
           //echo $gap;
	   $result = $mysqli->query("select * from 
	     ( select @row:=@row+1 as rownum, trade_date, closing_price 
	     from (select @row:=0) r, history where symbol=\"$ticker\"
	     and trade_date between \"$start\" and \"$end\") 
	     ranked where rownum % $gap = 0");
	   
	 } else {
	   $result = $mysqli->query("select trade_date, closing_price
	     from history
	     where symbol=\"$ticker\" and 
	     trade_date between \"$start\" and \"$end\";");
	 }
        

	 while ($row = $result->fetch_assoc()) {
           $keys[]=$row["trade_date"];
	   $values[]=$row["closing_price"];
	   //echo $row["trade_date"];
	   //echo $row["closing_price"];
	 }
         
	 $data = array_combine($keys, $values);
	echo print_r($data, true);
/*
        $graph = new PHPGraphLib(1000, 600);
        $graph->adddata($data);
        $graph->setTitle("Historical Prices for ". $_GET['ticker']);
        $graph->setBars(false);
        $graph->setLine(true);
        $graph->setDataPoints(false);

	if($count > 25)
		$graph->setXValuesInterval(5);
       
       $graph->setRange((int)max($values) + 5, (int)min($values) - 5);
       $graph->createGraph();

       */
