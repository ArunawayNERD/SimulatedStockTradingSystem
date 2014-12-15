<?php
/*
    input: a stock symbol or name

    output: an associative array containing company info whose names begin
      with the given string

   by default, it will print every stock from the stock table
*/


function stockSearch ($search) {

  require '/home/ssts/simulatedstocktradingsystem/public_html/creds.php';
  
  $mysqli = new mysqli($host, $user, $pass, $db);

  if($mysqli->connect_error)
    die($mysqli->connect_error);

  if($search=='') {
    $result = $mysqli->query('select symbol, name, last_trade_price,
      price_change from stocks;');
    
    $count=0;
    while($row = $result->fetch_assoc()) {
      $stock[$count] = array(
        "symbol"=>$row["symbol"], 
	"name"=>$row["name"],
	"last_trade_price"=>$row["last_trade_price"],
        "price_change"=>$row["price_change"]
      );
      $count++;
    } 
  } else {
    $search = "$search%";
    $stmt = $mysqli->prepare('select symbol, name, last_trade_price,
      price_change from stocks where name like ?
      or symbol like ?;');
    $stmt->bind_param('ss', $search, $search);
    $stmt->execute();
    $stmt->bind_result($row[0], $row[1], $row[2], $row[3]);
    
    $count=0;
    while($stmt->fetch()) {
      $stock[$count] = array(
        "symbol"=>$row[0], 
	"name"=>$row[1],
	"last_trade_price"=>$row[2],
        "price_change"=>$row[3]
      );
      $count++;
    }
    $stmt->close();
  } 
    $mysqli->close();

    return $stock;

}

?>
