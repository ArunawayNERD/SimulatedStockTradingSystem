<?php include 'session.php' ?>

<!doctype html>
<html>
<head>
  <title>SSTS - Home</title> 

   <script type="text/javascript" src="jquery-1.11.1.js"></script> 
   <script type="text/javascript" src="jquery.tablesorter.min.js"></script>
   <link rel="stylesheet" href="theme.blue.css">

  <link href="../dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="styles.css" rel="stylesheet">


</head>
<body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
       <div class="navbar-header">
           <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
           </button>
           <img class="navbar-brand" src="../ssts_logo.png" width="50" height="30"/>
       </div>
       <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
             <li <?php if ($_SERVER['QUERY_STRING'] == ""){echo "class='active'";} ?>><a href="index.php">Home</a></li>
             <li <?php if ($_SERVER['QUERY_STRING'] == "portfolios"){echo "class='active'";} ?>><a href="?portfolios">Portfolios</a></li>
             <li><a href="">Competitions</a></li>
			 <li <?php if ($_SERVER['QUERY_STRING'] == "stocks"){echo "class='active'";} ?>><a href="?stocks">Stocks</a></li>
             <li><a href="">What-If</a></li>
         </ul>
	 <ul class="nav navbar-nav navbar-right">
	    <li class="navbar-text visible-md visible-lg"><?php echo 'Welcome ' . $_SESSION['username'];?></li>
		<li><a href="">Settings</a></li>
	    <li><form method="post" action="logout.php">
	       <button type="submit" value="Log out" class="btn btn-default navbar-btn">Log out</button>
	       </form></li>
	 </ul>
      </div><!--/.nav-collapse -->
   </div>
</nav>

<div class="wrapper">
  <?php
    $available = array("portfolios","stocks"); 
    $request = $_SERVER['QUERY_STRING'];
    if($request=='') {
      include 'home.php';
    } else if (in_array($request, $available)) {
      include $request . '.php';
    }
  ?>

</div>

<footer class="footer" <?php if ($_SERVER['QUERY_STRING'] == "stocks"){echo "style='bottom:auto;'";} ?>>
   <div class="container">
      <p class="text-muted"><small>Team UG-2 - Fall 2014 - CS 324</small></p>
      <p class="text-muted"><small><a href="">About</a></small></p>
   </div>
</footer>

</div>

   <!-- Bootstrap core JavaScript -->
   <!-- Placed at the end of the document so the pages load faster -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
   <script src="../dist/js/bootstrap.min.js"></script>
   <script type="text/javascript" src="jquery-1.11.1.js"></script>
   <script type="text/javascript" src="jquery.tablesorter.min.js"></script>

   <script>
      $('#alphabet').on('click', 'li a', function() {
      if ($(this).text() == 'All') { //Show all of the stocks
         $('tbody > tr').show();
      }
      else {
      $('tbody > tr').show(); //Show all the stocks to reset
      // Grab the letter that was clicked
      var sCurrentLetter = $(this).text();
      // Now hide all rows that have IDs that do not start with this letter
      $('tbody > tr:not( [id^="' + sCurrentLetter + '"] )').hide();
      }
   });
   </script>

   <script type="text/javascript">        
      $(document).ready(function() {
         $("#stocks").tablesorter( {
            theme: 'default'
         });
      });
   </script>

</body>
</html>
