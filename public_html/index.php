
<?php
  require_once 'creds.php';
  $username=trim($_POST['username']);
  $password=trim($_POST['password']);
  $saltedPass=hash('ripemd128', "$username$password");
  
  $mysqli=new mysqli($host, $user, $pass, $db);
  if($mysqli->connect_error)
    die($mysqli->connect_error);

  $stmt=$mysqli->prepare('select * from users
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
  }

  $mysqli->close();
?>

<!doctype html>
<html>
<head>
  <title>SSTS</title> 
</head>
<body>

<h1>SSTS</h1>


<form method="post" action="index.php"> 
  Username: <input type="text" name="username" /> <br />
  Password: <input type="password" name="password" /> <br />
  	    <input type="submit" value="Login">
</form>

<?php
  if($username!='' || $password!='') {
    if($result[0]) { 
      echo 'Valid Login';
      header("Location: auth/index.php");
    }
    else
      echo 'Invalid Login';
  }
?>

<p><a href="registration.php">Register</a></p>


</body>

</html>
