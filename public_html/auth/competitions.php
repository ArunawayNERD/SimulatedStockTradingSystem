<?php
  /*
  echo "startDate: " . $_POST['startDate'] . "\n";
  echo "endDate: " . $_POST['endDate'] . "\n";
  echo "startTime: " . $_POST['startTime'] . "\n"; 
  echo "endTime: " . $_POST['endTime'] . "\n"; 
  $start = $_POST['startDate'] . " " . $_POST['startTime'];
  $end = $_POST['endDate'] . " " . $_POST['endTime'];
  echo "start " . $start . "\n";
  echo "end " . $end . "\n";
  echo "buyin: " . $_POST['buyin'] . "\n";
  echo "portfolio: " . $_POST['creator'] . "\n";
  echo "competition: " . htmlspecialchars($_POST['compName']) . "\n";
  */ 
  // functions that facilitate competitions
  include '/home/ssts/simulatedstocktradingsystem/competitions/'
    . 'CompetitionEngine.php';
  include_once '/home/ssts/simulatedstocktradingsystem/portfolios/'
    . 'PortfolioEngine.php';
  
  
  $uid = $_SESSION['id'];
  $portfolio = $_POST['portfolio'];
  if($portfolio != '') {
    setActivePortfolio($uid, $portfolio);
  } else {
    $portfolio = getActivePortfolio($uid, $portfolio);
    $_SESSION['active_portfolio'] = $portfolio;
  }

  // let's make a competition
  // first, sanitize input
  $compName=trim(htmlspecialchars($_POST['compName']));
  $buyin=trim(htmlspecialchars($_POST['buyin']));
  $startDate=$_POST['startDate'];
  $endDate=$_POST['endDate'];
  if ($compName!='' && $startDate!='' && $endDate!='' 
    && ctype_digit($buyin) && $buyin > 0) {
      $start = $startDate . " " . $_POST['startTime'];
      $end = $endDate . " " . $_POST['endTime'];
      $status = createComp($uid, $portfolio, $compName, $start,
        $end, $buyin);
      echo "Competition : " . $status;
  }
 
  echo "joinComp = " . $_POST['joinComp'];
  //join a competition
  if($_POST['joinComp']!='') {
    $status = addUser($_POST['joinComp'], $uid, $portfolio);
    echo $status;
  }
  // leave a competition
  if($_POST['leaveComp']!='') {
    leaveComp($_POST['leaveComp'], $uid);
  }
?>

<h1>Competitions: <?php echo $portfolio; ?></h1>

<form method="POST" action="index.php?competitions">
  <select name="portfolio">
    <?php
      echo "<option selected=\"selected\">";
      echo $portfolio . "</option>";
      $portfolios = getInactiveUserPortfolios($uid);
      foreach($portfolios as $port) {
        echo "<option value=\"" . $port[0] . "\">";
	echo $port[0];
	echo "</option>\n";
      }
    ?>
    <input type="submit" value="Select Portfolio" />
  </select>
</form>


<?php
  $cid = isCompeting($uid, $portfolio);
  if($portfolio != '' ) {
    if($cid == false) {
      include 'available_competitions.php';
    } else {
      include 'your_competitions.php';
    }
  }
  
?>



<?php include 'competitions_modals.php'; ?>

<script type="text/javascript">
  $(function() {
    $( ".datepicker" ).datepicker({dateFormat: "yy-mm-dd"});
  });
</script>
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
