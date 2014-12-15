<?php
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
}
