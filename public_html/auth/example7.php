<?php

/*
include('phpgraphlib.php');
include('/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php');

$mysqli = connectDB();
$prices = array();
$sql = 'SELECT closing_price, trade_date FROM history WHERE symbol = "GOOG"';
$result = $mysqli->query($sql);

$i=0;
while ($row = $result->fetch_assoc()) {
  $prices[$i] = $row;
  $i++;
}

echo $prices[4];
print_r2($prices);

echo '<br> this while loop is dumb';
$mysqli->close();

function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}nclude('phpgraphlib.php');
include('/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php');

$mysqli = connectDB();
$prices = array();
$sql = 'SELECT closing_price, trade_date FROM history WHERE symbol = "GOOG"';
$result = $mysqli->query($sql);

$i=0;
while ($row = $result->fetch_assoc()) {
  $prices[$i] = $row;
  $i++;
}

echo $prices[4];
print_r2($prices);

echo '<br> this while loop is dumb';
$mysqli->close();

function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}*/

include_once('phpgraphlib.php');
include_once('/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php');

$mysqli = connectDB();
$prices = array();
$sql = 'SELECT closing_price, trade_date FROM history WHERE symbol = "IBM"';
$result = $mysqli->query($sql);

while($row = $result->fetch_assoc())
{
  $keys[] = $row['trade_date'];
  $values[] = $row['closing_price'];
 }

$data = array_combine($keys, $values);

//$interval = (int)sizeof($data) / 25;
//echo $interval;

$graph = new PHPGraphLib(5000,400);
$graph->addData($data);
$graph->setTitle('PPM Per Container');
$graph->setBars(false);
$graph->setLine(true);
$graph->setXValuesInterval(10);
$graph->setDataPoints(false);
$graph->setDataPointColor('maroon');
//$graph->setDataValues(true);
$graph->setDataPointSize(3);
$graph->setDataValueColor('maroon');
$graph->createGraph();

