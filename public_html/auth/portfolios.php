<script type="text/javascript">
  $(document).ready(function() {
    $("#toggledBlock1").hide();
    $("#toggledBlock2").hide();
    $("#toggledBlock3").hide();
    $("#toggledBlock4").hide();

    $("#toggleBtn1").click(function() {
      $("#toggledBlock1").toggle();
    });
    $("#toggleBtn2").click(function() {
      $("#toggledBlock2").toggle();
    });
    $("#toggleBtn3").click(function() {
      $("#toggledBlock3").toggle();
    });
    $("#toggleBtn4").click(function() {
      $("#toggledBlock4").toggle();
    });
  });

</script>

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
?>

<!-- Overall Container -->
<div class="portfoliopage">

<!-- Container for Left Box -->
<div class="leftbox">

<div class="portinfo col-sm-3 col-md-2 sidebar">

<table class='nav nav-sidebar'>

<?php  
      // emphasize the active portfolio
        echo "<tr>"
	. "<td><em>" . $_SESSION['active_portfolio'] . "</em></td>"
	. "<td><em>" . $active_cash . "</em></td>"
	. "</tr>\n";
      // display the inactive portfolios 
    for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
      echo "<tr>" 
        . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	. "<td>" . $inactivePortfolios[$i][1] . "</td>"
	. "</tr>\n";
      }
  echo "</table>";
  echo "</div>";

?>
</div><!-- End of Left Box div -->

<!-- Container for Center Box -->
<div class="centerbox">
<!-- clicking the button shows the create portfolio form -->
<div>
<button class="btn btn-primary btn-small" id="toggleBtn1"> 
  Create portfolio 
</button>
</div>
<div id="toggledBlock1">
<form method="post" action="index.php?portfolios">
  <input type="text" name="newName" value="Portfolio Name" />
  <input type="submit" value="Make New" />
</form>
</div>

<!-- clicking the button shows the set active portfolio form -->
<div>
<button class="btn btn-primary btn-small" id="toggleBtn3"> 
  Set active Portfolio
</button>
</div>
<div id="toggledBlock3"> 
  <form method="post" action="index.php?portfolios">
    <table>
<?php
    // display the inactive portfolios as options for making active
    for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
      echo "<tr>" 
        . "<td><input type=\"radio\" name=\"active\" " 
	. "value=\"" . $inactivePortfolios[$i][0] . "\" /></td>" 
        . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	. "<td>" . $inactivePortfolios[$i][1] . "</td>"
	. "</tr>\n";
      }
?>
    </table>
    <input type="submit" value="Make Active"/>
  </form>
</div>

<!-- clicking the button shows the delete portfolio form -->
<div>
<button class="btn btn-primary btn-small" id="toggleBtn2"> 
  Delete portfolio 
</button>
</div>
<div id="toggledBlock2">
  <form method="post" action="index.php?portfolios">
    <table>
<?php
    for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
      echo "<tr>" 
        . "<td><input type=\"radio\" name=\"delete\" " 
	. "value=\"" . $inactivePortfolios[$i][0] . "\" /></td>" 
        . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	. "<td>" . $inactivePortfolios[$i][1] . "</td>"
	. "</tr>\n";
      }
?>
    </table>
    <input type="submit" value="Delete"/>
  </form>
</div>
<!-- clicking the button shows the rename portfolio form -->
<div>
<button class="btn btn-primary btn-small" id="toggleBtn4"> 
  Rename Portfolio
</button>
</div>
<div id="toggledBlock4"> 
  <form method="post" action="index.php?portfolios">
    <table>
<?php
    // display the inactive portfolios as options for renaming 
    for($i=0; $i<sizeOf($inactivePortfolios); $i++) {
      echo "<tr>" 
        . "<td><input type=\"radio\" name=\"rename\" " 
	. "value=\"" . $inactivePortfolios[$i][0] . "\" /></td>" 
        . "<td>" . $inactivePortfolios[$i][0] . "</td>"
	. "<td>" . $inactivePortfolios[$i][1] . "</td>"
	. "</tr>\n";
      }
?>
    </table>
    <input type="submit" value="Rename"/>
    <input type="hidden" name="isRename" value="true" />
  </form>
</div>
<?php
  if($_POST['isRename']==true) {
    echo "<form method=\"post\" action=\"index.php?portfolios\">\n";
    echo "<p>Rename " . $_POST['rename'] . " as </p>\n";
    echo "<input type=\"text\" name=\"renamedName\" />\n";
    echo "<input type=\"submit\" value=\"Rename\" />";
    echo "<input type=\"hidden\" name=\"changeName\" "
      . "value=\"" . $_POST['rename'] . "\" />";
    echo "</form>";
  }
  // debug
  /*
  echo "rename " . $_POST['rename'] . "<br/>";
  echo "changeName " . $_POST['changeName'] . "<br/>";
  echo "isRename " . $_POST['isRename'] . "<br/>";
  echo "renamedName " . $_POST['renamedName'] . "<br />";
  */
?>
</div> <!-- End of Center Box div -->

<!-- Container for Right Box -->
<div class="rightbox">
<p>Test</p>
</div> <!-- End of Right Box -->

</div> <!-- End of Overall Container -->