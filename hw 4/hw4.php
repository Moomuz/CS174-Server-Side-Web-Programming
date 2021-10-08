<?php
  //html code for webpage
  echo <<<_END
  <html>
  <title>HW #4</title>
  <body>
  <b>Shayanna Gatchalian
  </br> CS 174
  </br> 10.27.2020 </b>
  </br></br> <b>Assignment #4</b> </br>
  <form action="hw4.php" method ="post" enctype="multipart/form-data"><pre>
  Name <input type="text" name="Name"></br>
  Select File: <input type="file" name="Content" size="10"> <input type="submit" value="Submit">
  </pre></form>
  _END;

  //intro: include MySQL login credentials and connect to database
  require_once 'login.php';                                                                             //copies contents of login.php to this (hw4.php)
  $conn = new mysqli($hn, $un, $pw, $db);                                                               //establish connection to MySQL database
  if($conn->connect_error) die(fatal_sql_error("Cannot connect to database. Please try again."));

  //if there is user input, print error messages if insufficient input or save input to database
  if(isset($_POST['Name']) && $_POST['Name'] != '')                                                     //if user inputs non-empty name
  {
      $name = get_post($conn, 'Name');                                                                  //get name from textfield
      if(!empty($_FILES['Content']['tmp_name']) && file_exists($_FILES['Content']['tmp_name']))         //if user inputs (any) file
      {
        if($_FILES['Content']['type'] == 'text/plain')
        {
          $content = file_get_contents($_FILES['Content']['tmp_name']);                                 //get file contents
          $query = "INSERT INTO files VALUES" . "('$name', '$content')";
          $result = $conn->query($query);                                                               //save "Name" and "Content" to database, when user enters right inputs
          if(!$result)
            user_input_error("Error: Could not successfully save to database. Please try again.");
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
  }
  elseif($_FILES && file_exists($_FILES['Content']['tmp_name']))                                        //if user only inputs file
  {
    if($_FILES['Content']['type'] == 'text/plain')
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

  //query database and print table information
  $query = "SELECT * FROM files";                                                                       //load table "files"
  $result = $conn->query($query);
  if(!$result) die(fatal_sql_error("Could not read from database. Please try again."));

  $rows = $result->num_rows;                                                                            //iterate through table "files"
  for($i = 0; $i < $rows; $i++)
  {
    $result->data_seek($i);
    $row = $result->fetch_array(MYSQLI_ASSOC);
    echo 'Name: ' . $row['Name'] . '</br>';                                                             //print "Name" values (from textfield user input)
    echo 'File Contents: ' . $row['Content'] . '</br></br>';                                            //print "Content" (file content from user uploads)
  }

  //close connectiosn with database
  $result->close();                                                                                     //close connection to MySQL databse
  $conn->close();

  echo "</body></html>";

  //sanitizes user inputs for "Name" and "Content"
  function get_post($conn, $var)
  {
    return $conn->real_escape_string($_POST[$var]);
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

  //prints error message when user inputs wrong input
  function user_input_error($message)
  {
    echo "<b>Input Error: " . $message . "</b></br></br>";
  }

  //prints error message when MySQL error occurs
  function fatal_sql_error($message)
  {
    echo "<b>Database Error: " . $message . "</b></br></br>";
  }
?>
