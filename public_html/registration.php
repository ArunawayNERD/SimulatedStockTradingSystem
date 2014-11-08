<form method="post" action="registration.php"> 
  Username: <input type="text" name="username" />
  Password: <input type="password" name="password" />
  	    <input type="submit">
</form>

<?php
  require_once 'creds.php';
  $mysqli = new mysqli ($host, $user, $pass, $db);
  $username=trim($_POST['username']);
  $password=trim($_POST['password']);

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
      echo 'Thank you, ' . $username . ', for registering.';
    }
  } else {
    echo 'Please complete the form.';
  }
  $mysqli->close();
?>

<p><a href="http://pluto.hood.edu/~ssts">Home</a></p>
