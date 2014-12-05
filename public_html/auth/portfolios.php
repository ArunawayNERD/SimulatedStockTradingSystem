
<?php
  // include the proper logging mechanisms
  include 
    '/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php';

  // php methods that interface with the database
  include
    '/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php';
  
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
  //get active portfolio's cash
  $active_cash = getPortfolioCash($uid, $_SESSION['active_portfolio']);
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

<!-- Container for sidebar of portfolios -->
<div class="portinfo col-sm-3 col-md-2 sidebar">

<table class='nav nav-sidebar'>

<?php  
      // emphasize the active portfolio
        echo "<tr>"
	. "<td><em>" . $_SESSION['active_portfolio'] . "</em></td>"
	. "<td><em>$" . $active_cash . "</em></td>"
	. "</tr>\n";
      // display the inactive portfolios 
    for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
      echo "<tr>" 
        . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	. "<td>$" . $inactivePortfolios[$i][1] . "</td>"
	. "</tr>\n";
      }
  echo "</table>";
  echo "</div>";

?>
</div><!-- End of Left Box div -->

<!-- Container for Center Box -->
<div class="centerbox">

<?php
   echo "<h1>".$_SESSION['active_portfolio']."</h1>";
   echo "<h2>$".$active_cash."</h2>";
?>


<!-- clicking the button shows the delete portfolio form -->
<div>
<button class="btn btn-primary btn-small" id="toggleBtn2" data-toggle="modal" data-target="#deleteModal"> 
  Delete portfolio 
</button>
</div>
	  
<!-- clicking the button shows the rename portfolio form -->
<div>
<button class="btn btn-primary btn-small" id="toggleBtn4" 
  data-toggle="modal" data-target="#renameSelection"> 
  Rename Portfolio
</button>
</div>

<!-- current investments -->
<h2>Investment Portfolio</h2>
<table class="table">
  <tr>
    <th>Ticker</th>
    <th>Company</th>
    <th>Shares</th>
  </tr>
  <?php
    foreach($equities as $equity) {
      echo "<tr>";
      echo "<td>" . $equity->getStockSymbol() . "</td>";
      echo "<td>" . $equity->getStockSymbol() . "</td>";
      echo "<td>" . $equity->getStockSymbol() . "</td>";
      echo "</tr>";
    }
  ?>
</table>

<!-- list transactions -->
<h2>Transactions</h2>
<table class="table">
  <tr>
    <th>Time</th>
    <th>Portfolio</th>
    <th>Company</th>
    <th>Shares</th>
    <th>Price</th>
  </tr>
<?php 
  $transactions = getTransactions($uid);
  foreach($transactions as $transaction) {
    echo "<tr>";
    echo "<td>" . $transaction["ts"] . "</td>";
    echo "<td>" . $transaction["name"] . "</td>";
    echo "<td>" . $transaction["symbol"] . "</td>";
    echo "<td>" . $transaction["stocks"] . "</td>";
    echo "<td>$" . $transaction["sharePrice"] . "</td>";
    echo "</tr>";
  }
?>
</table>

</div> <!-- End of Center Box div -->


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
  <select name="buyStock" multiple size=20 class="form-control">
  <table>
  <?php
    include_once '/home/ssts/simulatedstocktradingsystem/stockSearch.php';
    $stockList = stockSearch($_POST['search']);
    for($i=0; $i<sizeOf($stockList); $i++) {
      echo "<tr>";
      echo "<option value=\"" . $stockList[$i][0] . "\">";
      echo "<td>" . $stockList[$i][0] . "</td>";
      echo "<td>" . $stockList[$i][1] . "</td>";
      echo "<td>" . $stockList[$i][2] . "</td>";
      echo "</option>";
      echo "</tr>\n";
    }
  ?>
  </table>
  </select>
  <button type="submit" class="btn btn-default" >
    Buy
  </button>
</form>



</div> <!-- End of Right Box -->

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
