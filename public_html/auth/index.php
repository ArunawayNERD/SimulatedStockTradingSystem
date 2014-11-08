<?php include 'session.php' ?>

<!doctype html>
<html>
<head>
  <title>SSTS</title> 
</head>
<body>

<form method="post" action="logout.php">
  <input type="submit" value="Log out" />
</form>

<h1>SSTS</h1>

<p>
<?php
  echo 'Welcome ' . $_SESSION['username'];
?>
</p>

<?php /*
  $available=array("stocks");
  $req=$_SERVER["QUERY_STRING"];
  if($req=="") {
  } else if (in_array($req, $available)) {
    include $req . '.php';
  }*/
  include 'stocks.php';
?>

</body>

</html>
