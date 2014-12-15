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

<div class="panel panel-default" id="home-activestats">
  <div class="panel-heading">
     <h3 class="panel-title">Active Portfolio Stats</h3>
  </div>
  <div class="panel-body">
    <?php
	   echo "<h2>".$_SESSION['active_portfolio']."</h2>";
	   echo "<p><bf>Cash:  </bf>".sprintf("$%.2f", $active_cash)."</p>";
	   echo("<p><bf>Value: </bf>".sprintf("$%.2f", $active_value)."</p>");
	?> 
  </div>
</div>

<div class="panel panel-default" id="home-leaderboard">
   <div class="panel-heading">
      <h3 class="panel-title">Global Leaderboard</h3>
   </div>
   <div class="panel-body overflow">
      <table class="table table-bordered">
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
   <div class="panel-body">
   
  
      <table class="table table-bordered">
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

		foreach($myComps as $comp)
		{
			$portName = getCompPortfolio($comp['cid'], $uid);
			$standing = getStanding($comp['cid'], $uid);
			echo('<tr>');
			echo('<td>'. $comp['name'] . '</td>');
			echo('<td>'. $portName['pname'].'</td>');
			echo('<td>'. $comp['start_time'].'</td>');
			echo('<td>'. $comp['end_time'] . '</td>');
			echo('<td>'. $standing . '</td>');
			echo('</tr>');
		}
	?>
      </table>
   </div>
</div>
