<!DOCTYPE html>
<html>
 <head>
  <title>Historical Stock Data</title>
 </head>
<body>

<h1>History</h1>

<?php

// make connection
$conn = new mysqli("pluto.hood.edu", "ssts", "oiBi2eph", "sstsdb");

// verify connection
if ($conn->connect_error) {
    die("Connection Failure: " . $conn->connect_error );
}

$result = $conn->query("Select * from history;");

  if ($result->num_rows > 0) {
    echo "<table><tr>
            <th>Symbol</th>
            <th>Company Name</th>
            <th>Last Trade Price</th>
            <th>Price at Open</th>
            <th>Change</th>
            <th>Last Trade Date</th>
            <th>Last Trade Time</th></tr>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
      echo "<tr><td>".$row["symbol"]."</td><td>".$row["name"]."</td>
                <td>".$row["last_trade_price"]."</td><td>".$row["open_price"]."</td>
                <td>".$row["price_change"]."</td><td>".$row["last_trade_date"]."</td>
                <td>".$row["last_trade_time"]."</td></tr>";
    }
    echo "</table>";
  } else {
    echo "0 results";
}

$conn->close();
?>

</body>
</html>