
<div class="row">
<div class="col-md-9">
<div class="row">
<div class="col-md-4">
<h2>Competition </h2>
  <h3> 
    <?php
      echo "Cash: " . sprintf("$%.2f" ,
        getPortfolioCash($uid, $compPortfolio)) . "<br />";
      echo "Value: " . sprintf("$%.2f" ,
        getValue($uid, $compPortfolio)); 
    ?>
 </div>  <!-- end col-mod-4 --> 
 <div class="col-md-4"> 
  <h3> Leaderboard </h3>
  <ol>
  <?php
     $top3 = getTopThree($cid);
     foreach($top3 as $top) {
       echo "<li>" . $top[0] . 
       ": <b>" . sprintf("$%.2f", $top[1]) . "</b></li>";

     }
  
  ?>
  </ol>
</div><!-- end col-md-4 -->
</div> <!-- end row -->
  </h3>
  <table class="table"> 
    <tr>
      <th>Competition</th>
      <th>Creator</th>
      <th>Start</th>
      <th>End</th>
      <th>Buy-in</th>
      <th>Status</th>
      <th></th>
    </tr>
    <?php
      $comp = getCompSettings($cid); 
        echo "<tr>";
	echo "<td>" . $comp['name'] . "</td>";
	echo "<td>" . $comp['creator'] . "</td>";
	echo "<td>" . $comp['start_time'] . "</td>";
	echo "<td>" . $comp['end_time'] . "</td>";
	echo "<td>" . sprintf("$%.2f",$comp['buyin']) . "</td>";
        if($comp['status']==0) {
          echo "<td>Waiting</td>";
	} elseif($comp['status']==1) {
          echo "<td>Ongoing</td>";
	}
	
	echo "<td>";
	if($comp['status']==0) {
	  echo "<form method=\"POST\" action=\"index.php?competitions\">";
	  echo "<input type=\"submit\" value=\"Leave\" class=\"btn btn-default\"/>";
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
    $equities = getAllStocks($uid, $compPortfolio);
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
    foreach($oppNames as $oppName) {  
      echo "<tr>";
      echo "<td>" . $oppName["pname"] . "</td>";  
      // get this opponent's stocks
      $stockList = getOpponentStocks($oppName["uid"], $oppName["pname"]);
      echo "<td>";
      if($stockList!=0) {
        echo "<ul>";
        foreach($stockList as $stock) {
          echo "<li>" . $stock . "</li>";
        }
        echo "</ul>";
      }
      echo "</td>";
      echo "<td>";
      $oppCompPort = getCompPortfolio($oppName["uid"], $oppName["pname"]);
      echo sprintf("$%.2f", getValue($oppName["uid"], $oppCompPort));
      echo "</td>";
      echo "</tr>";
    }

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
<div class="port-stocks">
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
      echo "<td onclick='buildGraph(\"" . $stock["symbol"] . "\")'>";
      echo "<a href=\"#\">" . $stock["symbol"] . "</a></td>"; 
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
<div id="dialog" >
  <img id="graph" src="" alt="graph" />
</div>
  <script type="text/javascript">
    function buildGraph(stock) {
      document.getElementById("graph").src="CurrentGraph.php?ticker="+stock;
      $("#dialog").dialog({width: 500});
    } 
  </script>
</div><!-- end port stocks -->
</div><!-- end row offset -->
</div><!-- end row class -->
