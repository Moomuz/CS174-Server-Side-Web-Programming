<?php
  //html code for introduction and Add section
  echo <<<_END
  <html>
  <title>Midterm #2</title>
  <body>
  <b>Shayanna Gatchalian
  </br> CS 174
  </br> 11.17.2020 </b>
  </br></br><b>Midterm #2</b>
  _END;

  //include database login credentials + connect to database
  require_once 'login.php';
  $conn = new mysqli($hn, $un, $pw, $db);
  if($conn->connect_error) die(db_conn_error("Cannot connect to database. Please try again. (1)"));
  $stmt = $conn->prepare('');

  if(isset($_POST['signup']))                                                                                 //display signup page
  {
    print_signup_page($conn);
  }
  elseif(isset($_POST['username']) && isset($_POST['password']))                                              //if username and password text fields are filled out
  {
    if(isset($_POST['signedup']))                                                                             //if user successfully signed up on 'Sign Up' page
    {
      add_user($conn);                                                                                        //then, create new user + save to database
      print_homepage();                                                                                       //print 'Homepage' once again
    }
    else
    {
      authenticate($conn);                                                                                    //elseif user signs in -> authenticate user
    }
  }
  else
  {
    print_homepage();                                                                                         //else, simply print 'Homepage'
  }

  //close connection with database
  $conn->close();                                                                                             //close overall connection to database

  //creates new user and saves credentials to database
  function add_user($conn)
  {
    $username = sanitize_mysql($conn, 'username');                                                            //get username
    if(isset($_POST['username']))
    {
      $password = sanitize_mysql($conn, 'password');                                                          //get password
      $salt1 = rand(1, 1000);
      $salt2 = rand(1, 1000);
      $token = hash('ripemd128', "$salt1$password$salt2");                                                    //salt + hash password
    }
    $stmt = $conn->prepare('INSERT INTO users VALUES(?, ?, ?, ?)');
    $stmt->bind_param('ssii', $username, $token, $salt1, $salt2);
    $stmt->execute();                                                                                         //save to 'users' table in database
    if ($stmt === FALSE) die (db_conn_error("Could not upload to database. Please try again."));
    else success_message("New account created. You may now log in.");
    $stmt->close();
  }

  //checks user sign in input against user credentials in database
  function authenticate($conn)
  {
    $un_temp = sanitize_mysql($conn, 'username');                                                             //get username
    $pw_temp = sanitize_mysql($conn, 'password');                                                             //get password
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $un_temp);
    $stmt->execute();                                                                                         //find username in 'users' table in database
    $result = $stmt->get_result();
    if ($stmt === FALSE) die (db_conn_error("Could not upload to database. Please try again."));
    elseif($result->num_rows)                                                                                 //if username is found
    {
      $row = $result->fetch_array(MYSQLI_NUM);
      $salt1 = $row[2];
      $salt2 = $row[3];
      $token = hash('ripemd128', "$salt1$pw_temp$salt2");                                                     //hash with inputted password + salts in database
      if($token == $row[1])                                                                                   //if 'token' matches with hash in database
      {
        print_upload_files_page($conn, $un_temp, $pw_temp);                                                   //print user-unique upload files page
      }
      else
      {
        die("Invalid username and/or password. Please try again.");
      }
    }
    else
    {
      die("Invalid username and/or password. Please try again.");
    }
    $stmt->close();
    $result->close();
  }

  //displays 'Sign Up' page
  function print_signup_page($conn)
  {
    echo <<<_END
    <form action="midterm2.php" method="post" enctype="multipart/form-data"><pre>
    Username <input type="text" name="username"></br>
    Password <input type="password" name="password"></br>
    <input type="hidden" name="signedup" value="signedup">
    <input type="submit" value="Sign Up">
    </pre></form>
    _END;
  }

  //displays 'Homepage'
  function print_homepage()
  {
    echo <<<_END
    <form action="midterm2.php" method="post" enctype="multipart/form-data"><pre>
    Username <input type="text" name="username"></br>
    Password <input type="password" name="password"></br>
    <input type="submit" value="Login">
    </pre></form>
    <form action="midterm2.php" method="post" enctype="multipart/form-data"><pre>
    <input type="hidden" name="signup" value="signup">
    <input type="submit" value="Click Here to Sign Up">
    </pre></form>
    _END;
  }

  //displays user-unique page (upload + display .txt files)
  function print_upload_files_page($conn, $un_temp, $pw_temp)
  {
    //upload .txt file section
    echo <<<_END
    </br></br><u>Upload Text File</u>
    <form action="midterm2.php" method ="post" enctype="multipart/form-data"><pre>
    Content Name <input type="text" name="contentname"></br>
    Select File: <input type="file" name="content" size="10"> <input type="submit" value="Submit">
    <input type="hidden" name="username" value="$un_temp">
    <input type="hidden" name="password" value="$pw_temp">
    </pre></form>
    _END;

    //if there is user input, print error messages if insufficient input or save input to database
    if(isset($_POST['contentname']) && $_POST['contentname'] != '')                                       //if user inputs non-empty name
    {
        $content_name = sanitize_mysql($conn, 'contentname');                                             //get name from textfield
        if(!empty($_FILES['content']['tmp_name']) && file_exists($_FILES['content']['tmp_name']))         //if user inputs (any) file
        {
          if($_FILES['content']['type'] == 'text/plain')
          {
            $content = file_get_contents($_FILES['content']['tmp_name']);                                 //get file contents
            $stmt = $conn->prepare('INSERT INTO files VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $content_name, $content, $un_temp);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($stmt === FALSE) die (db_conn_error("Could not upload to database. Please try again."));
          }
          else
          {
            user_input_error("Name and file inputted, but wrong file format. Please input .txt file.");   //user inputs both, but inputs wrong type of file
          }
        }
        else
        {
          user_input_error("Only name inputted. Please input .txt file.");                                //user only inputs non-empty name
          unset($_POST['Name']);                                                                          //reset name in $_POST
        }
        $stmt->close();
        $result->close();
    }
    elseif($_FILES && file_exists($_FILES['content']['tmp_name']))                                        //if user only inputs file
    {
      if($_FILES['content']['type'] == 'text/plain')
      {
        user_input_error("Only .txt file inputted. Please input name.");                                  //user only inputs .txt file
        reset_files_array();                                                                              //reset $_FILES
      }
      else
      {
        user_input_error("Only file inputted, but wrong file format. Please input name and .txt file.");  //user inputs wrong type of file
        reset_files_array();                                                                              //reset $_FILES
      }
    }

    echo "<u>Your Text Files</u></br>";
    $stmt = $conn->prepare('SELECT * FROM files');
    $stmt->execute();
    $result = $stmt->get_result();
    if ($stmt === FALSE) die (db_conn_error("Could not upload to database. Please try again."));

    $rows = $result->num_rows;                                                                            //iterate through table "files"
    for($i = 0; $i < $rows; $i++)
    {
      $result->data_seek($i);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      if($row['user'] == $un_temp)
      {
        echo 'Content Name: ' . $row['contentname'] . '</br>';                                            //print "Name" values (from textfield user input)
        echo 'File contents: ' . $row['content'] . '</br></br>';                                          //print "content" (file content from user uploads)
      }
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

  //resets $_FILES array, so no temporary file is stored in php code
  function reset_files_array()
  {
    unset($_FILES['name']);
    unset($_FILES['tmp_name']);
    unset($_FILES['size']);
    unset($_FILES['type']);
    unset($_FILES['error']);
  }

  //prints success message when user input is successfully uploaded to database
  function success_message($message)
  {
    echo "</br></br><b>Success! </b>" . $message;
  }

  //prints error message when user inputs wrong input
  function user_input_error($message)
  {
    echo "<b>Input Error: " . $message . "</b></br></br>";
  }

  //prints error message when MySQL error occurs
  function db_conn_error($message)
  {
    echo "<b>Database Error: " . $message . "</b></br></br>";
  }

  //close HTML tags
  echo "</html></body>";
?>
