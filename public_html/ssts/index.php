
<?php
  session_start();
  if(!isset($_SESSION['username'])) {
    header('HTTP/1.0 401 Unauthorized');
    die("Unauthorized Access");
  }
?>

<!doctype html>
<html>
<head>
  <title>SSTS</title> 
</head>
<body>

<form method="post" action="../logout.php">
  <input type="submit" value="Log out" />
</form>

<h1>SSTS</h1>

<p>
<?php
  echo 'Welcome ' . $_SESSION['username'];
?>
</p>

</body>

</html>
