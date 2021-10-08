<?php
  //html code for introduction
  echo <<<_END
  <html>
  <title>Final :'(</title>
  <body>
  <b>Shayanna Gatchalian
  </br> CS 174
  </br> 12.15.2020 </b>
  </br></br><b>Final :'(</b>
  _END;

  //include database login credentials + connect to database
  require_once 'login.php';
  $conn = new mysqli($hn, $un, $pw, $db);
  if($conn->connect_error) die(db_conn_error("Cannot connect to database. Please try again. (1)"));
  $stmt = $conn->prepare('');

  //display login form
  if(!isset($_POST['username']) || !isset($_POST['password']))
  {
    echo <<<_END
    <form action="lame_proj_login.php" method="post" enctype="multipart/form-data"><pre>
    Username <input type="text" name="username"></br>
    Password <input type="password" name="password"></br>
    <input type="submit" value="Login">
    </pre></form>
    <form action="lame_proj_signup.php" method="post" enctype="multipart/form-data"><pre>
    <input type="submit" value="Click Here to Sign Up">
    </pre></form>
    _END;
  }

  if(isset($_POST['username']) && isset($_POST['password']))                                                            //if username and password text fields are filled out
  {
    authenticate($conn);                                                                                                //then, attempt to authenticate user
  }

  //checks user sign in input against user credentials in database
  function authenticate($conn)
  {
    $un_temp = sanitize_mysql($conn, 'username');                                                                       //get username...
    $pw_temp = sanitize_mysql($conn, 'password');                                                                       //... and get password...
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $un_temp);
    $stmt->execute();                                                                                                   //... then, find username in 'users' table in database
    $result = $stmt->get_result();
    if ($stmt === FALSE) die (db_conn_error("</br></br>Could not upload to database. Please <a href=lame_proj_login.php>click here</a> to try again."));
    elseif($result->num_rows)                                                                                           //if matching username is found
    {
      $row = $result->fetch_array(MYSQLI_NUM);
      $salt1 = $row[2];
      $salt2 = $row[3];
      $token = hash('ripemd128', "$salt1$pw_temp$salt2");                                                               //hash inputted password + salts in database...
      if($token == $row[1])                                                                                             //... and if 'token' matches with hash in database...
      {
        session_start();
        $_SESSION['username'] = $un_temp;
        $_SESSION['password'] = $pw_temp;
        echo "</br></br>Hello, you are now logged in.";
        die("<p><a href=lame_proj_index.php>Click here to continue</a></p>");                                           ///... then, log in authenticated user
      }
      else //password is not autenticated
      {
        die("</br></br>Invalid username and/or password. Please <a href=lame_proj_login.php>click here</a> to try again.");
      }
    }
    else //username is not authenticated
    {
      die("</br></br>Invalid username and/or password. Please <a href=lame_proj_login.php>click here</a> to try again.");
    }
    $stmt->close();
    $result->close();
  }

  //sanitizes user inputs for potential html injection attacks
  function sanitize_string($var)
  {
    $var = stripslashes($var);
    $var = strip_tags($var);
    $var = htmlentities($var);
    return $var;
  }

  //sanitizes user inputs for MySQL queries
  function sanitize_mysql($conn, $var)
  {
    $var = $conn->real_escape_string($_POST[$var]);
    $var = sanitize_string($var);
    return $var;
  }

  echo "</body></html>";
?>
