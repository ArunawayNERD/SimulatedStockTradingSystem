
<!--    Registration Form             -->
<form method="post" action="registration.php"> 
  Username: <input type="text" name="username" />
  Password: <input type="password" name="password" />
  	    <input type="submit">
</form>
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
      echo $username . ' has already registered.';
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
      echo 'Thank you, ' . $username . ', for registering.';
    }
  } else {
    echo 'Please complete the form.';
  }
  $mysqli->close();
?>

<!-- Link to the login page           --> 
<p><a href="http://pluto.hood.edu/~ssts">Home</a></p>
