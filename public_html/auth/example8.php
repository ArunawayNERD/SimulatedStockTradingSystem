<?php
include('phpgraphlib.php');
include('/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php');

$i = 0;
$dates = array();
$prices = array();

$mysqli = connectDB();

$sql = 'SELECT closing_price FROM history WHERE symbol = "GOOG"';

if ($result = $mysqli->query($sql)) 
  printf("Select returned %d rows. \n", $result->num_rows);
print_r($result);

/*
while($row = $result->fetch_assoc()) 
{
  //get the column names into an array $colNames
  if($i == 0) { 
    foreach($row as $colname => $val)
    $dates[] = $colname;
  }
  //get the data into an array
  $prices[] = $row;
  $i++;
}

print_r2($prices);
print_r2($dates);
*/
echo '<br> this while loop is dumb';
$mysqli->close();

function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}
