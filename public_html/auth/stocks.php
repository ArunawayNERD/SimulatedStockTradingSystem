<?php include 'session.php' ?>
<!doctype html>
<html>
<head>
  <title>SSTS - Stocks</title> 

  <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="navbar-fixed-top.css" rel="stylesheet">

</head>
<body>
    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
       <div class="navbar-header">
           <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
           </button>
           <img class="navbar-brand" src="../ssts_logo.png" width="50" height="30"/>
       </div>
       <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
             <li><a href="index.php">Home</a></li>
             <li><a href="">About</a></li>
             <li class="active"><a href="">Stocks</a></li>
             <li><a href="">Portfolios</a></li>
             <li><a href="">Competitions</a></li>
             <li><a href="">What-If</a></li>
         </ul>
	 <ul class="nav navbar-nav navbar-right">
	    <li><form method="post" action="logout.php">
	       <button type="submit" value="Log out" class="btn btn-default navbar-btn">Log out</button>
	       </form></li>
	 </ul>
      </div><!--/.nav-collapse -->
   </div>
</nav>

<div class="wrapper" id="stocks-wrapper">
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
    echo "<table class='table table-striped table-hover'><thead><tr>
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

   <!-- Bootstrap core JavaScript -->
   <!-- Placed at the end of the document so the pages load faster -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
   <script src="../dist/js/bootstrap.min.js"></script>
   <script>
      $('#alphabet').on('click', 'li a', function() {
      if ($(this).text() == 'All') { //Show all of the stocks
         $('tbody > tr').show();
      }
      else {
      $('tbody > tr').show(); //Show all the stocks to reset
      // Grab the letter that was clicked
      var sCurrentLetter = $(this).text();
      // Now hide all rows that have IDs that do not start with this letter
      $('tbody > tr:not( [id^="' + sCurrentLetter + '"] )').hide();
      }
   });
   </script>
</body>
</html>
