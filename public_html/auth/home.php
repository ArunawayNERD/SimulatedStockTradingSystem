<?php
  // php methods that interface with the database
  include
    '/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php';
  
  // get user id from session variables 
  $uid=$_SESSION['id'];
  
  //get and set as session variable the active portfolio 
  $_SESSION['active_portfolio'] = getActivePortfolio($uid);
  //get active portfolio's cash
  $active_cash = getPortfolioCash($uid, $_SESSION['active_portfolio']);
  $active_value = getValue($uid, $_SESSION['active_portfolio']);
?>
<head>
<style>
.homebox{
	max-height: 15em;
	overflow: scroll;
}

.homeboxleaders{
	max-height: 30em;
	overflow: scroll;
}

</style>
</head>
<div class="panel panel-default" id="home-activestats">
  <div class="panel-heading">
     <h3 class="panel-title">Active Portfolio Stats</h3>
  </div>
  <div class="panel-body homebox">
    <?php
	   echo "<h2>".$_SESSION['active_portfolio']."</h2>";
	   echo "<p><bf>Cash:  </bf>".sprintf("$%.2f", $active_cash)."</p>";
	   echo("<p><bf>Value: </bf>".sprintf("$%.2f", $active_value)."</p>");
	?>
	</br>

    	<table class="table">
	<tr>
		<th>Ticker</th>
		<th>Company</th>
		<th>Shares</th>
	</tr>
	
	<?php
		require_once "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";

		$stocks = getAllStocks($uid, $_SESSION['active_portfolio']);
	
		if(sizeof($stocks) > 0)
		{
		foreach($stocks as $stock)
		{
			echo('<tr>');
			echo('<td>'.$stock->getStockSymbol().'</td>');
			echo('<td>'.$stock->getStockName().'</td>');
			echo('<td>'.$stock->getNumShares().'</td>');
			echo('</tr>');
		}
		}
	?>
	</table>
    </div>
</div>

<div class="panel panel-default" id="home-leaderboard">
   <div class="panel-heading">
      <h3 class="panel-title">Global Leaderboard</h3>
   </div>
   <div class="panel-body homeboxleaders">
      <table class="table">
	<tr>
		<th>Rank</th>
		<th>Portfolio Name</th>
		<th>Value</th>
	</tr>
	<?php
			require_once "/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php";

			$topTen = getTopTen();

			foreach($topTen as $row)
			{
				echo('<tr>');
				echo('<td>'. $row[2] . '</td>');
				echo('<td>' . $row[0] . '</td>');
				echo('<td>'. sprintf("$%.2f", $row[1]) .'</td>');
				echo('</tr>');
			}
		?>	
	</table>
	</div>
</div>

<div class="panel panel-default" id="home-compbox">
   <div class="panel-heading">
      <h3 class="panel-title">Your Competitions</h3>
   </div>
   <div class="panel-body homebox">
      <table class="table">
      <tr>
		<th>Competition Name</th>
		<th>Your Portfolio</th>
		<th>Start Time</th>
		<th>End Time</th>
		<th>Your Position</th>
      </tr>
      <?php

      		require_once "/home/ssts/simulatedstocktradingsystem/competitions/CompetitionEngine.php";

		$myComps = listUsersCurrentComps($uid);

		if(sizeof($myComps) > 0)
		{
		foreach($myComps as $comp)
		{

			$portName = getCompPortfolios($comp['cid'], $uid);
			$standing = getStanding($comp['cid'], $uid);
			echo('<tr>');
			echo('<td>'. $comp['name'] . '</td>');
			echo('<td>'. $portName['pname'].'</td>');
			echo('<td>'. $comp['start_time'].'</td>');
			echo('<td>'. $comp['end_time'] . '</td>');
			echo('<td>'. $standing . '</td>');
			echo('</tr>');
      		}
		}
	?>	
      </table>
      </div>
</div>
<div style="clear:both;">
</div>
