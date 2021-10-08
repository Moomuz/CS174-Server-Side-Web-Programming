<?php
  //html code for introduction and Add section
  echo <<<_END
  <html>
  <title>HW #5</title>
  <body>
  <b>Shayanna Gatchalian
  </br> CS 174
  </br> 11.3.2020 </b>
  </br></br><b>Assignment #5</b></br>
  </br></br><u>Add</u>
  <form action="hw5.php" method ="post" enctype="multipart/form-data"><pre>
  Advisor Name <input type="text" name="advisorname"> (ex: John Smith)</br>
  Student Name <input type="text" name="studentname"> (ex: Anna Brown)</br>
  Student ID <input type="text" name="studentid"> (ex: 012345678)</br>
  Class Code <input type="text" name="classcode"> (ex: 01234)</br>
  <input type="submit" value="Add Info">
  </pre></form>
  _END;

  //include MySQL login credentials and connect to database
  require_once 'login.php';                                                                             //copies contents of login.php to this (hw5.php)
  $conn = new mysqli($hn, $un, $pw, $db);                                                               //establish connection to MySQL database
  if($conn->connect_error) die(fatal_sql_error("Cannot connect to database. Please try again."));

  //prepare sql query statement
  $stmt = $conn->prepare('INSERT INTO roster VALUES(?, ?, ?, ?, ?)');
  $stmt->bind_param('ssssi', $advisor_name, $student_name, $student_id, $class_code, $null);

  //if there is user input, print error messages if insufficient input or save input to database
  $valid_input = false;                                                                                 //$valid_input = false, until all inputs have gone through checks
  if(isset($_POST['advisorname']) && $_POST['advisorname'] != '' &&
     isset($_POST['studentname']) && $_POST['studentname'] != '')                                       //series of checks for valid user input
  {
    $advisor_name = sanitize_mysql($conn, 'advisorname');
    $student_name = sanitize_mysql($conn, 'studentname');                                               //get advisorname and studentname
    if(isset($_POST['studentid']) && $_POST['studentid'] != '')
    {
      if(ctype_digit($_POST['studentid']) == false)                                                     //if studentid contains non-numeric digits, input invalid
      {
        user_input_error("Student ID contains non-numeric values. Please input 9-digit number and try again.");
      }
      elseif($_POST['studentid'] > 999999999)                                                           //if studentid is more than 9 digits, input invalid
      {
        user_input_error("Student ID is more than 9 digits. Please input 9-digit number and try again.");
      }
      else
      {
        $student_id = sanitize_mysql($conn, 'studentid');                                               //get studentid
      }
      if(isset($_POST['classcode']) && $_POST['classcode'] != '')
      {
        if(ctype_digit($_POST['classcode']) == false)                                                   //if classcode contains non-numeric digits, input invalid
        {
          user_input_error("Class Code contains non-numeric values. Please input 5-digit number and try again.");
        }
        elseif($_POST['classcode'] > 99999)                                                             //if classcode is more than 5 digits, input invalid
        {
          user_input_error("Class Code is more than 9 digits. Please input 5-digit number and try again.");
        }
        else
        {
          $class_code = sanitize_mysql($conn, 'classcode');                                             //get classcode
          $valid_input = true;                                                                          //all inputs are checked and valid, ok to save to database
        }
      }
    }
  }

  //save validated inputs to database
  if($valid_input == true)
  {
    $leading_zeros = "";                                                                                //adds leading zeros to studentid
    for($i = 0; $i < 9 - strlen($student_id); $i++)
    {
      $leading_zeros .= "0";
    }
    $student_id = $leading_zeros . $student_id;

    $leading_zeros = "";                                                                                //adds leading zeros to classcode
    for($i = 0; $i < 5 - strlen($class_code); $i++)
    {
      $leading_zeros .= "0";
    }
    $class_code = $leading_zeros . $class_code;

    $stmt->execute();                                                                                   //executes prepared statement to MySQL
    if ($stmt === FALSE) die (fatal_sql_error("Could not upload to database. Please try again."));
    else success_message("Data uploaded to database.");
  }

  //html code for Search section
  echo <<<_END
  </br><u>Search</u>
  <form action="hw5.php" method ="post" enctype="multipart/form-data"><pre>
  Advisor Name <input type="text" name="searchadvisor"></br>
  <input type="submit" value="Search">
  </pre></form>
  _END;

  //query database for Advisor
  $query = "SELECT * FROM roster";                                                                       //load table "roster"
  $result = $conn->query($query);
  if(!$result) die(fatal_sql_error("Could not read from database. Please try again."));

  $advisor_found = false;
  $rows = $result->num_rows;
  if(isset($_POST['searchadvisor']) && $_POST['searchadvisor'] != '')
  {
    for($i = 0; $i < $rows; $i++)                                                                         //iterate through table "roster"
    {
      $result->data_seek($i);
      $row = $result->fetch_array(MYSQLI_ASSOC);
      if($advisor_found == true)                                                                          //prints only studentname, studentid, classcode
      {
        if(strcasecmp($row['advisorname'], $_POST['searchadvisor']) == 0)
        {
          echo 'Student Name: ' . $row['studentname'] . '</br>';
          echo 'Student ID: ' . $row['studentid'] . '</br>';
          echo 'Class Code: ' . $row['classcode'] . '</br></br>';
          $advisor_found = true;
        }
      }
      elseif(strcasecmp($row['advisorname'], $_POST['searchadvisor']) == 0)                                //prints advisorname only once, then studentname, studentid, classcode
      {
        echo '<b>Advisor Found: ' . $row['advisorname'] . '</b></br></br>';
        echo 'Student Name: ' . $row['studentname'] . '</br>';
        echo 'Student ID: ' . $row['studentid'] . '</br>';
        echo 'Class Code: ' . $row['classcode'] . '</br></br>';
        $advisor_found = true;
      }
    }
    if($advisor_found == false)                                                                            //advisor not found, prints 404 error message
    {
      four_zero_four_error("Advisor not found. Please try again.");
    }
  }

  //close connectiosn with MySQL database
  $stmt->close();
  $result->close();
  $conn->close();

  //close HTML tags
  echo "</body></html>";

  //prints success message when user input is successfully uploaded to database
  function success_message($message)
  {
    echo "<b>Success! </b>" . $message . "</br></br>";
  }

  //prints error message when MySQL error occurs
  function fatal_sql_error($message)
  {
    echo "<b>Database Error: </b>" . $message . "</br></br>";
  }

  //prints error message when user inputs are wrong format
  function user_input_error($message)
  {
    echo "<b>Input Error: </b>" . $message . "</br></br>";
  }

  //prints error message when Advisor is not found
  function four_zero_four_error($message)
  {
    echo "<b>404 Error: </b>" . $message . "</br></br>";
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
?>
