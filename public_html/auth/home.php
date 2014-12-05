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
?>

<div class="panel panel-default" id="home-activestats">
  <div class="panel-heading">
     <h3 class="panel-title">Active Portfolio Stats</h3>
  </div>
  <div class="panel-body">
    <?php
	   echo "<h2>".$_SESSION['active_portfolio']."</h2>";
	   echo "<p><bf>Cash:</bf> ".$active_cash."</p>";
	?> 
  </div>
</div>

<div class="panel panel-default" id="home-leaderboard">
   <div class="panel-heading">
      <h3 class="panel-title">Leaderboard</h3>
   </div>
   <div class="panel-body">
      Placeholder
   </div>
</div>

<div class="panel panel-default" id="home-compbox">
   <div class="panel-heading">
      <h3 class="panel-title">Your Competitions</h3>
   </div>
   <div class="panel-body">
      Placeholder
   </div>
</div>