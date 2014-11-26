<div id="stocks-wrapper">
<div class="stocks-wrapper">
<h1>Current Stock Prices</h1>

<div class="stocks-nav">
<ul id="alphabet">
   <li><a href="javascript:;">All</a></li>
   <li><a href="javascript:;">A</a></li>
   <li><a href="javascript:;">B</a></li>
   <li><a href="javascript:;">C</a></li>
   <li><a href="javascript:;">D</a></li>
   <li><a href="javascript:;">E</a></li>
   <li><a href="javascript:;">F</a></li>
   <li><a href="javascript:;">G</a></li>
   <li><a href="javascript:;">H</a></li>
   <li><a href="javascript:;">I</a></li>
   <li><a href="javascript:;">J</a></li>
   <li><a href="javascript:;">K</a></li>
   <li><a href="javascript:;">L</a></li>
   <li><a href="javascript:;">M</a></li>
   <li><a href="javascript:;">N</a></li>
   <li><a href="javascript:;">O</a></li>
   <li><a href="javascript:;">P</a></li>
   <li><a href="javascript:;">Q</a></li>
   <li><a href="javascript:;">R</a></li>
   <li><a href="javascript:;">S</a></li>
   <li><a href="javascript:;">T</a></li>
   <li><a href="javascript:;">U</a></li>
   <li><a href="javascript:;">V</a></li>
   <li><a href="javascript:;">W</a></li>
   <li><a href="javascript:;">X</a></li>
   <li><a href="javascript:;">Y</a></li>
   <li><a href="javascript:;">Z</a></li>
</ul>
</div>

<?php

// make connection
include '../creds.php'; 
$conn = new mysqli($host, $user, $pass, $db);

// verify connection
if ($conn->connect_error) {
  die("Connection Failure: " . $conn->connect_error );
}

$result = $conn->query("Select * from stocks;");

  if ($result->num_rows > 0) {
    echo "<table class='table table-striped table-hover tablesorter' id='stocks'><thead><tr>
            <th>Symbol</th>
            <th>Company Name</th>
            <th>Trade Price</th>
            <th>Opening Price</th>
            <th>Change</th>
            <th>Last Trade Date</th>
            <th>Last Trade Time</th></tr></thead><tbody>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
      echo "<tr id='".$row["symbol"]."'>
        <td>".$row["symbol"]."</td>
	<td>".$row["name"]."</td>
        <td>".$row["last_trade_price"]."</td>
	<td>".$row["open_price"]."</td>
        <td>".$row["price_change"]."</td>
	<td>".$row["last_trade_date"]."</td>
        <td>".$row["last_trade_time"]."</td>
      </tr>";
    }
    echo "</tbody></table>";
  } else {
    echo "0 results";
}

$conn->close();
?>

</div>
</div>
