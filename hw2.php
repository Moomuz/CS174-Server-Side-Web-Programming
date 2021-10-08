<?php
  echo "<b>Shayanna Gatchalian
  </br> CS 174
  </br> 9.24.2020 </b>
  </br></br> <b>Assignment #2</b> </br>";

  tester();                                                             //run tester function

  //converts Roman numeral number (I,II,III) to Hindu-Arabic number (1,2,3)
  function roman_converter($roman_num)
  {
    $roman_letters = array("", "", "M", "D", "C", "L", "X", "V", "I");  //used for roman letter lookup, first 2 indexes used for offset in inner for-loop
    $numerals = array(0, 0, 1000, 500, 100, 50, 10, 5, 1);              //used for roman to hindu-arabic conversion, first 2 indexes used for offset in inner for-loop
    $total_sum = 0;                                                     //total running sum of roman numeral when split into decimal places (i.e., 100+10+1=111)
    $temp_sum = 0;                                                      //represents decimal place value before adding to $total_sum (e.g., 10 in 111)
    $roman_num .= " ";                                                  //prevents uninitialized string offset error when calculating $roman_num[$i+1]

    $i = 0;
    while($i < strlen($roman_num))
    {
      $letter = $roman_num[$i];                                       //$letter = pointer
      for($j = 2; $j < count($roman_letters); $j++)                   //find $letter in $roman_letters, start at 2 because of offset
      {
        if($letter == $roman_letters[$j])                             //matching letter is found
        {
          $temp_sum = $numerals[$j];                                  //convert to hindu-arabic value
          if($roman_num[$i+1] == $roman_letters[$j-1])                //if value is 4(IV), 40(XL), 400(CD)
          {
            $fours = $numerals[$j-1] - $temp_sum;                     //calculate value "fours" value
            $total_sum += $fours;                                     //add to $total_sum
            $i++;                                                     //skip letter
          }
          elseif($roman_num[$i+1] == $roman_letters[$j-2])            //if value is 9(IX), 90(XC), 900(CM)
          {
            $nines = $numerals[$j-2] - $temp_sum;                     //calculate value "nines" value
            $total_sum += $nines;                                     //add to $total_sum
            $i++;                                                     //skip letter
          }
          else
          {
            $total_sum += $temp_sum;                                  //else, add to $total_sum
          }
        }
      }
      // echo "</br>" . $letter . "=". $temp_sum . " > " . $total_sum;
      $i++;                                                           //iterate
    }
    echo "</br>Input: " . $roman_num . "> Output: " . $total_sum;
  }

  //Checks if input is a valid roman numeral
  function is_valid($roman_num)
  {
    if($roman_num == "")                                                    //empty string
    {
      echo "</br>Invalid input: Input is empty. Please try again.";
    }
    elseif(is_string($roman_num) == false)                                  //not a string
    {
      echo "</br>Invalid input: '" . $roman_num . "' is not a string.
      Please try again.";
    }
    elseif(is_string($roman_num) == true)                                   //is a string, continue further checks
    {
      $valid = true;
      $count = 0;
      $letter = $roman_num[0];
      for($i = 0; $i < strlen($roman_num); $i++)                            //letters other than M, D, C, L, X, V, I are not valid
      {
        if($roman_num[$i] == "A" || $roman_num[$i] == "B" || $roman_num[$i] == "E" ||
           $roman_num[$i] == "F" || $roman_num[$i] == "G" || $roman_num[$i] == "H" ||
           $roman_num[$i] == "J" || $roman_num[$i] == "K" || $roman_num[$i] == "N" ||
           $roman_num[$i] == "O" || $roman_num[$i] == "P" || $roman_num[$i] == "Q" ||
           $roman_num[$i] == "R" || $roman_num[$i] == "S" || $roman_num[$i] == "T" ||
           $roman_num[$i] == "U" || $roman_num[$i] == "W" || $roman_num[$i] == "Y" ||
           $roman_num[$i] == "Z")
        {
          $valid = false;
          echo "</br>Invalid input: '" . $roman_num . "' is not a valid roman numeral.
          Please try again.";
        }
        if($roman_num[$i] == $letter)                                       //starts checks for repeated letters
        {
          $count++;
          if($letter == "V" || $letter == "L" || $letter == "D")            //cannot repeat V, L, D
          {
            if($count == 2)
            {
              $valid = false;
              echo "</br>Invalid input: '" . $roman_num . "' is not a valid roman numeral. "
              . $letter . " is repeated twice. Please try again.";
            }
          }
          if($count == 4)                                                   //letters cannot repeat more than 3 times
          {
            $valid = false;
            echo "</br>Invalid input: '" . $roman_num . "' is not a valid roman numeral. "
            . $letter . " is repeated more than 3 times. Please try again.";
          }
        }
        else                                                                //else, continue counting repeated letter
        {
          $count = 0;
          $letter = $roman_num[$i];
        }
      }
      if($valid == true)                                                    //if passes all tests, continue to roman_converter function
      {
        roman_converter($roman_num);
      }
    }
  }

  function tester()
  {
    is_valid("");           //tests empty is_string
    is_valid(25);           //not a string
    is_valid("Hello");      //tests invalid string
    is_valid("XXXX");       //not standard roman numeral
    is_valid("VV");         //cannot repeat V,L,D
    is_valid("XXX");        //30, tests uninitialized string offset error
    is_valid("CCCLIV");     //360
    is_valid("MMXXI");      //2021
    is_valid("MMMCMXCIX");  //3999
  }
?>
