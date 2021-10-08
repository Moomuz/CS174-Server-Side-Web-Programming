<?php
  echo "<b>Shayanna Gatchalian
  </br> CS 174
  </br> 10.6.2020 </b>
  </br></br> <b>Midterm #1</b> </br>";

  upload();                                                                           //for user to upload .txt file
  tester();                                                                           //at end of this php file

  //user uploads file to website
  function upload()
  {
    echo <<<_END
      <html>
        <head><title>Upload Text File</title></head>
        <body>
          <form method='post' action='midterm1.php' enctype='multipart/form-data'>
            Select File: <input type='file' name='filename' size='10'>
            <input type='submit' value='Upload'>
          </form>
    _END;

    if($_FILES)
    {
      $name = $_FILES['filename']['tmp_name'];
      if(!empty($_FILES['filename']['tmp_name'] && file_exists($_FILES['filename']['tmp_name'])))        //if file has been uploaded BUT not saved to server
      {
        echo "<b>User Input:</b></br>";
        concatenate($name, file_get_contents($name));
      }
    }
    echo "</body></html>";
  }

  //concatenates file contents into 1 long string
  function concatenate($name, $file)
  {
    $file = file($name, FILE_IGNORE_NEW_LINES);                                   //$file right now is an array of strings (each string is a new line in file)
    $string = "";                                                                 //$string = concatenated lines from file
    for($i = 0; $i < count($file); $i++)                                          //for each line in the file
    {
      $string = $string . $file[$i];                                              //concatenate each line
    }
    sanitize($string);
  }

  //sanitzes input (removes spaces and new lines)
  function sanitize($string)
  {
    $string = str_replace(array("\n", "\r", " "), '', $string);                   //removes spaces from $string
    validate($string);
  }

  //validates input before formatting
  function validate($string)
  {
    $i = 0;
    while($i < strlen($string)-1)                                                 //check if input is only made up of numbers
    {
      if($string[$i] == "0" || $string[$i] == "1" || $string[$i] == "2" || $string[$i] == "3" ||
         $string[$i] == "4" || $string[$i] == "5" || $string[$i] == "6" ||
         $string[$i] == "7" || $string[$i] == "8" || $string[$i] == "9")
      {
        $valid = true;
      }
      else
      {
        $valid = false;                                                           //if non-numeric char is found, print error message and break loop
        echo "Input contains '" . $string[$i] . "', which is not a number. Please try again.</br>";
        break;
      }
      $i++;
    }
    if($valid == true && strlen($string) < 400)                                                     //if input is less than 400 char, ask user to add more numbers
    {
      $difference = 400 - strlen($string);
      $valid = false;
      echo "Input is length of " . strlen($string) . ". Please add " . $difference . " more numbers.</br>";
    }
    if($valid == true)                                                            //if pass both validation tests, continue program
    {
      format($string);
    }
  }

  //format $string into 20x20 array and also prints onto webpage
  function format($string)
  {
    //format into 20x20 array
    $array = array();                                                             //$array = 20x20 array
    for($i = 0; $i < 20; $i++)                                                    //create empty 20x20 array
    {
      $array[$i] = array();
      for($j = 0; $j < 20; $j++)
      {
        $array[$i][$j] = " ";
      }
    }

    //print the array
    echo "Input as 20x20 array:</br>";
    $str_pos = 0;                                                                 //$str_pos = position of current character in $string
    for($row = 0; $row < count($array); $row++)                                   //fill array with input
    {
      for($col = 0; $col < count($array[0]); $col++)
      {
        $array[$row][$col] = $string[$str_pos];
        $str_pos++;
        echo $array[$row][$col] . " ";
      }
      echo "</br>";
    }
    find_max($array);
  }

  function find_max($array)
  {
    $factors = array();                                                           //$factors = 4 test factors to be multiplied
    $max_factors = array();                                                       //$max_factors = the 4 factors that yield the max product
    $max_product = 0;                                                             //$max_product = max product from $max_factors

    //across
    for($row = 0; $row < count($array)-3; $row++)
    {
      for($col = 0; $col < count($array[0])-3; $col++)
      {
        array_push($factors, $array[$row][$col]);
        array_push($factors, $array[$row][$col+1]);
        array_push($factors, $array[$row][$col+2]);
        array_push($factors, $array[$row][$col+3]);
        $product = multiply($factors);
        if($product > $max_product)
        {
          $max_product = $product;
          $max_factors = $factors;
        }
        $factors = array();
      }
    }

    //vertical
    for($col = 0; $col < count($array)-3; $col++)
    {
      for($row = 0; $row < count($array[0])-3; $row++)
      {
        array_push($factors, $array[$row][$col]);
        array_push($factors, $array[$row+1][$col]);
        array_push($factors, $array[$row+2][$col]);
        array_push($factors, $array[$row+3][$col]);
        $product = multiply($factors);
        if($product > $max_product)
        {
          $max_product = $product;
          $max_factors = $factors;
        }
        $factors = array();
      }
    }

    //diagonal (top-left to bottom-right)
    for($row = 0; $row < count($array)-3; $row++)
    {
      for($col = 0; $col < count($array[0])-3; $col++)
      {
        array_push($factors, $array[$row][$col]);
        array_push($factors, $array[$row+1][$col+1]);
        array_push($factors, $array[$row+2][$col+2]);
        array_push($factors, $array[$row+3][$col+3]);
        $product = multiply($factors);
        if($product > $max_product)
        {
          $max_product = $product;
          $max_factors = $factors;
        }
        $factors = array();
      }
    }

    //diagonal (top-right to bottom-left)
    for($row = 0; $row < count($array)-3; $row++)
    {
      for($col = 3; $col < count($array[0]); $col++)
      {
        array_push($factors, $array[$row][$col]);
        array_push($factors, $array[$row+1][$col-1]);
        array_push($factors, $array[$row+2][$col-2]);
        array_push($factors, $array[$row+3][$col-3]);
        $product = multiply($factors);
        if($product > $max_product)
        {
          $max_product = $product;
          $max_factors = $factors;
        }
        $factors = array();
      }
    }

    echo "</br>Max product = " . $max_factors[0] . " x " . $max_factors[1] . " x " . $max_factors[2] . " x " . $max_factors[3] . " = " . $max_product . "</br>";
  }

  function multiply($factors)
  {
    $product = $factors[0] * $factors[1] * $factors[2] * $factors[3];
    return $product;
  }

  //tester function
  function tester()
  {
    echo "</br><b>Test 1: Invalid Input (Contains Non-Numeric Value)</b></br>";
    $test = sanitize("Hello World!");

    echo "</br><b>Test 2: Invalid Input (Not Enough Numbers)</b></br>";
    $test = sanitize("0123456789");

    echo "</br><b>Test 3: Across</b></br>";
    $test = sanitize("00000000012340000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000");

    echo "</br><b>Test 4: Vertical</b></br>";
    $test = sanitize("00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000010000
                      00000000000000020000
                      00000000000000030000
                      00000000000000040000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000");

    echo "</br><b>Test 5: Diagonal (Top-Left to Bottom-Right)</b></br>";
    $test = sanitize("00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000001000
                      00000000000000000200
                      00000000000000000030
                      00000000000000000004");

    echo "</br><b>Test 6: Reverse Diagonal (Top-Right to Bottom-Left)</b></br>";
    $test = sanitize("00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000000000
                      00000000000000001000
                      00000000000000020000
                      00000000000000300000
                      00000000000004000000");
  }

?>
