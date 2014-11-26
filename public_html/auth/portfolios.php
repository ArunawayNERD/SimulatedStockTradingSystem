<?php
//  include 
//    '/home/ssts/simulatedstocktradingsystem/portfolios/PortfolioEngine.php';

  // include the proper logging mechanisms
  include 
    '/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php';
 
  if($_POST['active']!='')
    $_SESSION['active_portfolio'] = $_POST['active'];
  
  // connect to the database
  require_once '../creds.php';
  $mysqli = new mysqli ($host, $user, $pass, $db);
  
  // check for connection error
  if($mysqli->connect_error) 	
    die($mysqli->connect_error);
  
  // get user id from session variables 
  $uid=$_SESSION['id'];
 
  //get portfolios from database that match the user id
  $result=$mysqli->query("select name, cash from portfolios
    where uid=$uid");
  
  //Sidebar
  if($result->num_rows > 0) {
    echo "<div class='col-sm-3 col-md-2 sidebar'>";
    echo "<ul class='nav nav-sidebar'>";
    while($row = $result->fetch_assoc()) {
       echo "<li><a href=''>".$row["name"]."</a></li>";
    }
    echo "</ul></div>";
  }
  
  // only display the results if there are any
  if($result->num_rows > 0) {
    echo "<form method=post action=index.php?portfolios>";
    echo "<table>";
    while($row = $result->fetch_assoc()) {
      // display the active portfolio in bold
      if($_SESSION['active_portfolio']==$row["name"]) {
        echo "<tr><td></td>"
	. "<td><em>" . $row["name"] . "</em></td>"
	. "<td><em>" . $row["cash"] . "</em></td>"
	. "</tr>";
      } else { // display the remaining portfolios as options
      echo "<tr>" 
        . "<td><input type=radio name=active " 
	. "value=" . $row["name"] . " /></td>" 
        . "<td>" . $row["name"] . "</td>"
	. "<td>" . $row["cash"] . "</td>"
	. "</tr>";
      }
    }
    echo "</table>";
    echo "<input type=submit />";
    echo "</form>";
  }
 
  echo $_SESSION['active_portfolio'];

  $mysqli->close();

?>
