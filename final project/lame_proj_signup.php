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
  if($conn->connect_error) die(error_message("Cannot connect to database. Please try again. (1)"));
  $stmt = $conn->prepare('');

  //display sign up form
  echo <<<_END
  <form action="lame_proj_signup.php" method="post" enctype="multipart/form-data"><pre>
  Username <input type="text" name="username"></br>
  Password <input type="password" name="password"></br>
  <input type="hidden" name="signedup" value="signedup">
  <input type="submit" value="Sign Up">
  </pre></form>
  <form action="lame_proj_login.php" method="post" enctype="multipart/form-data"><pre>
  <input type="submit" value="Back to Log In">
  </pre></form>
  _END;

  if(isset($_POST['signedup']))                                                                                //if user successfully signed up on 'Sign Up' page
  {
    add_user($conn);                                                                                           //then, create new user + save to database                                                                                      //print 'Homepage' once again
  }

  //creates new user and saves credentials to database
  function add_user($conn)
  {
    $username = sanitize_mysql($conn, 'username');                                                            //get username...
    if(isset($_POST['username']))
    {
      $password = sanitize_mysql($conn, 'password');                                                          //... and get password...
      $salt1 = rand(1, 1000);
      $salt2 = rand(1, 1000);
      $token = hash('ripemd128', "$salt1$password$salt2");                                                    //... and salt + hash password...
    }
    $stmt = $conn->prepare('INSERT INTO users VALUES(?, ?, ?, ?)');
    $stmt->bind_param('ssii', $username, $token, $salt1, $salt2);
    $stmt->execute();                                                                                         //... then, finally save to 'users' table in database
    if ($stmt === FALSE) die (db_conn_error("Could not upload to database. Please try again."));
    else success_message("New account created. Please click the 'Back to Log In' button to log in.");
    $stmt->close();
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

  //prints success message when user input is successfully uploaded to database
  function success_message($message)
  {
    echo "</br></br><b>Success! </b>" . $message;
  }

  echo "</body></html>";
?>
