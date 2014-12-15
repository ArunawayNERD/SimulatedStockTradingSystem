
<?php
  // include the proper logging mechanisms
  //include 
  //  '/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php';

  // php methods that interface with the database
  include realpath('../../portfolios/PortfolioEngine.php');
 
  // for transactions
  include realpath('../../transactions/TransactionEngine.php');

  // get user id from session variables 
  $uid=$_SESSION['id'];

  // make a new portfolio
  if(trim($_POST['newName']) != '') {
    makeNewPortfolio($uid, $_POST['newName']);
  }
  // set the new active portfolio if submitted
  if($_POST['active']!='')
    setActivePortfolio($uid, $_POST['active'] );

  // deletes portfolio if submitted
    if($_POST['delete'] != '') 
      deletePortfolio($uid,$_POST['delete']);

  // renames portfolio if submitted
  if($_POST['changeName'] != '' && trim($_POST['renamedName'] != '')) { 
    changePortfolioName($uid,$_POST['changeName'],
      trim($_POST['renamedName']));
  }

  //get and set as session variable the active portfolio 
  $_SESSION['active_portfolio'] = getActivePortfolio($uid);

  // purchase a stock 
  if(is_numeric($_POST['numSharesBuy']) && $_POST['numSharesBuy']>0) {
    buyStock($uid, $_SESSION['active_portfolio'], 
      $_POST['buyStock'], $_POST['numSharesBuy']); 
  }
  // sell a stock 
  if(is_numeric($_POST['numSharesSell']) && $_POST['numSharesSell']>0) {
    sellStock($uid, $_SESSION['active_portfolio'], 
      $_POST['sellMe'], $_POST['numSharesSell']); 
  }
  //get active portfolio's cash
  $active_cash = getPortfolioCash($uid, $_SESSION['active_portfolio']);
  //calculate the value of the active portfolio's assets
  $active_value = getValue($uid, $_SESSION['active_portfolio']);
  // get the inactive portfolios
  $inactivePortfolios = getInactiveUserPortfolios($uid);
  //get the active portfolio's investments
  $equities = getAllStocks($uid, $_SESSION['active_portfolio']);
?>

<!-- Overall Container -->
<div class="portfoliopage">

<!-- Container for Left Box -->
<div class="leftbox">
<!-- clicking the button shows the create portfolio modal -->
<div class="side-button">
<button class="btn btn-primary btn-small" id="toggleBtn1" data-toggle="modal" data-target="#createModal"> 
  Create portfolio 
</button>
</div>

<!-- clicking the button shows the set active portfolio modal -->
<div class="side-button">
<button class="btn btn-primary btn-small" id="toggleBtn3" data-toggle="modal" data-target="#activeModal"> 
  Set active Portfolio
</button>
</div>

<div class="dropdown" id="sidebar-colp">
  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
    Portfolio
    <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
 <?php  
      // emphasize the active portfolio
	echo "<li role='presentation'><a href='' role='menuitem'><em><span style='float:left;width:50%;'>" . $_SESSION['active_portfolio']. "</span><span style='float:right;width:50%;'>$" . sprintf("%.2f",$active_cash) . "</span></em></a></li>\n";
      // display the inactive portfolios 
    for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
      echo "<li role='presentation'><a href='' role='menuitem'><span style='float:left;width:50%;'>" . $inactivePortfolios[$i][0] . "</span><span style='float:right;width:50%;'>$" . 
      sprintf("%.2f",$inactivePortfolios[$i][1]) . "</span></a></li>\n";
      }
?>   
  </ul>
</div> <!-- End of sidebar-colp div -->

<!-- Container for sidebar of portfolios -->
<div class="portinfo sidebar" id="sidebar-exp">

<ul class='nav nav-sidebar'>

<?php  
      // emphasize the active portfolio
	echo "<li class='sidebar-active'><a href=''><em><span style='float:left;width:50%;'>" . $_SESSION['active_portfolio']. "</span><span style='float:right;width:50%;'>$" . sprintf("%.2f",$active_cash) . "</span></em></a></li>\n";
      // display the inactive portfolios 
    for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
      echo "<li><a href=''><span style='float:left;width:50%;'>" . $inactivePortfolios[$i][0] . "</span><span style='float:right;width:50%;'>$" . 
      sprintf("%.2f",$inactivePortfolios[$i][1]) . "</span></a></li>\n";
      }
?>
</ul>

</div> <!-- End of sidebar div -->

</div> <!-- End of Leftbox div -->

<div class="rightbox">

<h3>Buy Shares</h3>

<!-- search bar -->
<form class="form-inline" method="POST" action="index.php?portfolios">
  <input type="text" name="search" />
  <button type="submit" class="btn btn-sm" >
    Search
  </button>
</form>

<!-- selection menu of stocks, filtered by the above search menu --> 
<form method="POST" action="index.php?portfolios"> 
  <table class="table">  
    <tr>
      <th>Ticker</th>
      <th>Co.</th>
      <th>Price</th>
      <th>Change</th>
    </tr>
  </table>
  <select name="selectStock" size=20 class="form-control">
  <?php
    include_once '/home/ssts/simulatedstocktradingsystem/stockSearch.php';
    $stockList = stockSearch($_POST['search']);
    foreach($stockList as $stock) {
      echo "<option value=\"" . $stock["symbol"] . "\">";
      echo $stock["symbol"]; 
      echo $stock["last_trade_price"];
      echo $stock["price_change"];
      echo "</option>\n";
    }
  ?>
  </select>
  <button type="submit" class="btn btn-default" >
    Buy
  </button>
</form>
<p>
 <?php 
/*
$numShares=$_POST['numShares'];
  if (is_numeric($numShares))
    echo "is_numeric=true\n";
  else
    echo "is_numeric=false\n";

  echo 'selectStock:' . $_POST['selectStock'] . "\n"; 
  echo 'buyStock:' . $_POST['buyStock'] . "\n"; 
  echo 'numShares:' . $_POST['numShares'] . "\n"; 
  */  
  ?>
</p>

</div> <!-- End of Rightbox -->

<!-- Container for Center Box -->
<div class="centerbox">

<?php
   echo "<h1>".$_SESSION['active_portfolio']."</h1>";
?>

<!-- clicking the button shows the rename portfolio form -->
<div>
<button class="btn btn-primary btn-small" id="toggleBtn4" 
  data-toggle="modal" data-target="#renameSelection"> 
  Rename Portfolio
</button>
</div>
 
<div class="port-stats">
<?php
   echo "<h2>Cash = $
   " . sprintf("%.2f", $active_cash) . "</h2>";
?>
<h2>
  Value = $
  <?php echo $active_value; ?>
</h2>
</div> <!-- End port-stats div -->

<!-- current investments -->
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
      echo "<td><form method=\"POST\" action=\"index.php?portfolios\">";
      echo "<input type=\"hidden\" name=\"sellStock\" ";
      echo "value=\"" . $equity->getStockSymbol() . "\" />";
      echo "<input type=\"submit\" value=\"Sell\" />";
      echo "</form></td>";
      echo "</tr>\n";
    }
  ?>
</table>

<!-- list transactions -->
<h3>Transactions</h3>
<table class="table table-bordered">
  <tr>
    <th>Time</th>
    <th>Ticker</th>
    <th>Shares</th>
    <th>Price</th>
  </tr>
<?php 
  $transactions = getTransactions($uid, $_SESSION['active_portfolio']);
  foreach($transactions as $transaction) {
    echo "<tr>";
    echo "<td>" . $transaction["ts"] . "</td>";
    echo "<td>" . $transaction["symbol"] . "</td>";
    echo "<td>" . $transaction["stocks"] . "</td>";
    echo "<td>$" . $transaction["sharePrice"] . "</td>";
    echo "</tr>";
  }
?>
</table>

<!-- clicking the button shows the delete portfolio form -->
<div>
<button class="btn btn-primary btn-small" id="toggleBtn2" data-toggle="modal" data-target="#deleteModal"> 
  Delete portfolio 
</button>
</div>
</div> <!-- End of Center Box div -->

</div> <!-- End of Overall Container -->

<!-- for all the fancy modals -->
<?php include 'portfolios_modals.php'; ?>
  
<?php  
  // display rename modal after rename selection modal is submitted
  if($_POST['isRename']==true) {
    
    echo "<script type=\"text/javascript\">";
    echo "$('#renameModal').modal('show');";
    echo "</script>";
 
  }
?>  
<?php  
  // display buy stock modal as  needed 
  if($_POST['selectStock']!='') {
    
    echo "<script type=\"text/javascript\">";
    echo "$('#stockBuyModal').modal('show');";
    echo "</script>";
 
  }
?>  
<?php  
  // display sell stock modal as  needed 
  if($_POST['sellStock']!='') {
    
    echo "<script type=\"text/javascript\">";
    echo "$('#stockSellModal').modal('show');";
    echo "</script>";
 
  }
?>  
