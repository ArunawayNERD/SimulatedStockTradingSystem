
<?php
  require_once 'creds.php';
  $username=trim($_POST['username']);
  $password=trim($_POST['password']);
  $saltedPass=hash('ripemd128', "$username$password");
  
  $mysqli=new mysqli($host, $user, $pass, $db);
  if($mysqli->connect_error)
    die($mysqli->connect_error);

  $stmt=$mysqli->prepare('select id, username, password from users
    where username=? and password=?');
  $stmt->bind_param('ss', $username, $saltedPass);
  $stmt->execute();
  $stmt->bind_result($result[0],$result[1],$result[2]);
  $stmt->fetch();
  $stmt->close();
  if($result[0]) {
    session_start();
    $_SESSION['id'] = $result[0];
    $_SESSION['username'] = $result[1];
    $_SESSION['password'] = $result[2];
    $_SESSION['active_portfolio']; 
    // to log logins
    //require dirname('../Logging/LoggingEngine.php');
    include 
      '/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php';
    $log=new LoggingEngine();
    $log->logUserLogin($_SESSION['username']);
  }
  
  $mysqli->close();
?>

<!doctype html>
<html>
<head>
  <title>SSTS - Login</title>

  <link href="dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="signin.css" rel="stylesheet"> 
</head>
<body>

<img src="ssts_logo.png" class="logo" width="240" height="144"/>

<h1 class="form-signin-heading">Simulated Stock <br/>Trading System</h1>
<?php
  if($username!='' || $password!='') {
      if($result[0]) { 
         echo 'Valid Login';
	 header("Location: auth/index.php");
      }
      else
         echo '<span class="signin-message">Invalid Login</span>';
      }
?>

<form method="post" action="index.php" class="form-signin"> 
  <input type="text" name="username" class="form-control" placeholder="Username" required autofocus/> <br />
  <input type="password" name="password" class="form-control" placeholder="Password" required/> <br />
  <input type="submit" value="Login" class="btn btn-lg btn-primary btn-block">
</form>
<div class="signin-options">
<p class="split-para"><a href="recover_login.php">Forgot Login?</a><span><a href="registration.php">Register</a></span></p>
</div>

</body>

</html>
