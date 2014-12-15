
<div class="row">
<div class="col-md-9">
<h2>Competition </h2>
  <table class="table"> 
    <tr>
      <th>Creator</th>
      <th>Competition</th>
      <th>Start</th>
      <th>End</th>
      <th>Buy-in</th>
    </tr>
    <?php
      $comp = getCompSettings($cid); 
        echo "<tr>";
	echo "<td>" . $comp['creator'] . "</td>";
	echo "<td>" . $comp['name'] . "</td>";
	echo "<td>" . $comp['start_time'] . "</td>";
	echo "<td>" . $comp['end_time'] . "</td>";
	echo "<td>" . sprintf("$%.2f",$comp['buyin']) . "</td>";
        echo "<td>";
	if($comp['status']==0) {
	  echo "<form method=\"POST\" action=\"index.php?competitions\">";
	  echo "<input type=\"submit\" value=\"Leave\" />";
	  echo "<input type=\"hidden\" name=\"leaveComp\" ";
	  echo "value=\"" . $comp['cid']  . "\" />";
	  echo "</form>";
	}
	echo "</td>";
	echo "</tr>\n";
    ?>
  </table>
<h3>Shares</h3>
<table class="table table-bordered">
  <tr>
    <th>Ticker</th>
    <th>Company</th>
    <th>Shares</th>
    <th></th>
  </tr>
  <?php
    foreach($equities as $equity) {
      echo "<tr>";
      echo "<td>" . $equity->getStockSymbol() . "</td>";
      echo "<td>" . $equity->getStockName() . "</td>";
      echo "<td>" . $equity->getNumShares() . "</td>";
      echo "<td><form method=\"POST\" action=\"index.php?competitions\">";
      echo "<input type=\"hidden\" name=\"sellStock\" ";
      echo "value=\"" . $equity->getStockSymbol() . "\" />";
      echo "<input type=\"submit\" value=\"Sell\" />";
      echo "</form></td>";
      echo "</tr>\n";
    }
  ?>
</table>

  <h3>Opponents</h3>
  <table class="table">
    <tr>
      <th>Name</th>
      <th>Investments</th>
      <th>Value</th>
    </tr>
  <?php 
    $oppNames = getOpponentNames($uid, $portfolio);
    echo "<tr>";
    foreach($oppNames as $oppName) {  
      echo "<td>" . $oppName["pname"] . "</td>";  
    }
    echo "<td></td><td></td>";  
    echo "</tr>";

  ?>
  </table>

<?php include 'winners.php' ?>

</div> <!-- end main column --> 
<div class="col-md-3">

<h3>Buy Shares</h3>

<!-- search bar -->
<form class="form-inline" method="POST" action="index.php?competitions">
  <input type="text" name="search" />
  <button type="submit" class="btn btn-sm" >
    Search
  </button>
</form>

<!-- selection menu of stocks, filtered by the above search menu --> 
<div id="port-stocks">
  <table class="table">
    <tr>
      <th>Ticker</th>
      <th>Price</th>
      <th>Change</th>
      <th></th>
    </tr>
  <?php
    include_once '/home/ssts/simulatedstocktradingsystem/stockSearch.php';
    $stockList = stockSearch($_POST['search']);
    foreach($stockList as $stock) {
      echo "<tr>";
      echo "<td>" . $stock["symbol"] . "</td>"; 
      echo "<td>" . $stock["last_trade_price"] . "</td>";
      echo "<td>" . $stock["price_change"] . "</td>";
      echo "<td><form method=\"POST\" action=\"index.php?competitions\">";
      echo "<input type=\"submit\" value=\"Buy\" />"; 
      echo "<input type=\"hidden\" name=\"selectStock\" ";
      echo "value=\"" . $stock["symbol"] . "\" />";
      echo "</form></td>";
      echo "</tr>\n";
    }
  ?>
  </table>
</div><!-- end port stocks -->
</div><!-- end row offset -->
</div><!-- end row class -->
