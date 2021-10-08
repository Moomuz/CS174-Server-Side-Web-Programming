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

  session_start();
  if(isset($_POST['logout']))                                                                                                                   //if user clicks Logout button
  {
    destroy_session_and_data();                                                                                                                 //then, end session and go back to login page
  }
  elseif(isset($_SESSION['username']))                                                                                                          //else, display index page
  {
    $username = $_SESSION['username'];
    echo "</br></br>Welcome back, " . $username;

    //Upload Dictionaries Section
    echo <<<_END
    </br></br><b>Upload Dictionaries</b>
    <form action="lame_proj_index.php" method="post" enctype="multipart/form-data"><pre>
    Second Language               <input type="text" name="second_language_name"></br>
    English Dictionary            <input type="file" name="english" size="10"></br>
    Second Language Dictionary    <input type="file" name="second_language" size="10"></br>
    <input type="submit" value="Upload Dictionaries">
    </pre></form>
    _END;

    if(isset($_POST['second_language_name']) && $_POST['second_language_name'] != '')                                                           //if 'Second Language' textbox is filled out
    {
      $second_language = sanitize_mysql($conn, 'second_language_name');                                                                         //then, save 2nd language name as $var
      if(!empty($_FILES['english']['tmp_name']) && file_exists($_FILES['english']['tmp_name']) &&
         !empty($_FILES['second_language']['tmp_name']) && file_exists($_FILES['second_language']['tmp_name']))                                 //if user has uploaded both English and SL dictionaries...
      {
        if($_FILES['english']['type'] == 'text/plain' || $_FILES['second_language']['type'] == 'text/plain')                                    //... and they are .txt format
        {
          $eng_file = file($_FILES['english']['tmp_name'], FILE_IGNORE_NEW_LINES);
          $sl_file = file($_FILES['second_language']['tmp_name'], FILE_IGNORE_NEW_LINES);
          for($i = 0; $i < count($eng_file); $i++)                                                                                              //then, read each line of each file...
          {
            $eng_word = sanitize_string($eng_file[$i]);
            $sl_word = sanitize_string($sl_file[$i]);
            $stmt = $conn->prepare('INSERT INTO dictionaries VALUES (?, ?, ?, ?)');                                                             //... and insert as entry in database
            $stmt->bind_param('ssss', $username, $second_language, $eng_word, $sl_word);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($stmt === FALSE) die (error_message("Could not upload to database. Please try again."));
          }
        }
        else //user uploads wrong file format
        {
          error_message("Wrong file format uploaded. Please only upload .txt files.");
        }
      }
      else //user uploads one or neither dictionaries
      {
        error_message("Could not upload both files. Please try again.");
      }
    }
    elseif($_FILES) //'Second Language' text box is not filled in by user
    {
      error_message("Second language name not inputted. Please try again");
    }

    //Traslate Section
    echo <<<_END
    <b>Translate</b>
    <form action="lame_proj_index.php" method="post" enctype="multipart/form-data"><pre>
    Choose Language               <input type="text" name="chosen_language_name"></br>
    <textarea name="english_input" rows="4" cols="50">Input English phrase here...</textarea></br>
    <input type="submit" value="Translate">
    </pre></form>
    <div style="width: 376px; height: 66px; border: solid #bdbdbd 1px; border-radius: 2px; margin: 0; font-family: monospace;">
    _END;

    if(isset($_POST['english_input']) && $_POST['english_input'] != '' &&
       isset($_POST['chosen_language_name']) && $_POST['chosen_language_name'] != '')                                                           //if chosen dictionary and English phrase are correctly inputted and is not empty
    {
      $eng_input = sanitize_mysql($conn, 'english_input');
      $eng_array = explode(" ", $eng_input);                                                                                                    //then, split phrase into individual words...
      $sl_name_input = sanitize_mysql($conn, 'chosen_language_name');
      $sl_output = '';
      for($i = 0; $i < count($eng_array); $i++)
      {
        $stmt = $conn->prepare('SELECT * FROM dictionaries WHERE secondlanguagename = ? AND englishword = ?');
        $stmt->bind_param('ss', $sl_name_input, $eng_array[$i]);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($stmt === FALSE) die (error_message("Could not upload to database. Please try again."));
        elseif($result->num_rows)
        {
        $row = $result->fetch_array(MYSQLI_NUM);                                                                                                //... and query each English word to the database...
        }
        $sl_output .= $row[3] . " ";                                                                                                            //... and add each second language word to a string var...
      }
      echo "<p style='margin: 0px;'>" . $sl_output . " </p>";                                                                                   //... and print translated phrase on page
    }

    //Logout
    echo <<<_END
    </div>
    <form action="lame_proj_login.php" method="post" enctype="multipart/form-data"><pre>
    <input type="hidden" name="logout" value="logout">
    <input type="submit" value="Logout">
    </pre></form>
    _END;
  }
  else //session is not fresh
  {
    echo "</br></br>Please <a href='lame_proj_login.php'>click here</a> to log in.";
  }

  //destroys session and data
  function destroy_session_and_data()
  {
    $_SESSION = array();
    setcookie(session_name(), '', time()-2592000, '/');
    session_destroy();
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

  //prints generic error message
  function error_message($message)
  {
    echo "<b>Error: " . $message . "</b></br></br>";
  }

  //close HTML tags
  echo "</body></html>";
?>
