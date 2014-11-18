<!DOCTYPE html>
<html>
<head>
   <title>SSTS - Register</title>

    <link href="dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="signin.css" rel="stylesheet">
</head>

<body>

<img src="ssts_logo.png" class="logo" width="240" height="144"/>

<h1 class="form-signin-heading">Simulated Stock <br/>Trading System</h1>

<?php
  // include the proper logging mechanisms
  include 
    '/home/ssts/simulatedstocktradingsystem/Logging/LoggingEngine.php';
  
  // connect to the database
  require_once 'creds.php';
  $mysqli = new mysqli ($host, $user, $pass, $db);
  
  // get username and password entered
  // trim whitespaces at the front and back
  $username=trim($_POST['username']);
  $password=trim($_POST['password']);

  // check for connection error
  if($mysqli->connect_error) 	
    die($mysqli->connect_error);

  // check to see if the form was completed
  if ($username!='' && $password !='') {
    // check to see if the username is already taken
    $stmt=$mysqli->prepare('select username 
      from users where username = ?;');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($result);
    $stmt->fetch();
    $stmt->close();
    if($result)
      echo '<span class="signin-message">' . $username . ' has already registered.</span>';
    else {
      // add new username to database
      $token=hash('ripemd128', "$username$password");
      $stmt=$mysqli->prepare("insert into users (username, password )
        values (?,?);");
      $stmt->bind_param('ss', $username, $token);
      $stmt->execute();
      $stmt->close();
      // logs the new users
      $log=new LoggingEngine();
      $log->logUserRegistration($username);
      echo '<span class="signin-message">Thank you, ' . $username . ', for registering.</span>';
    }
  } else {
    echo '<span class="signin-message">Please complete the form.</span>';
  }
  $mysqli->close();
?>

<!--    Registration Form             -->
<form method="post" action="registration.php" class="form-signin"> 
   <input type="text" name="username" class="form-control" placeholder="Username" required autofocus/> <br/>
   <input type="password" name="password" class="form-control" placeholder="Password" required/> <br/>
   <input type="submit" class="btn btn-lg btn-primary btn-block">
</form>

<!-- Link to the login page           --> 
<p class="signin-message"><a href="http://pluto.hood.edu/~ssts">Home</a></p>
</body>
</html>
