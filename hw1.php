<?php
  tester();                                             //run tester function

  //finds the prime numbers up to $input and prints them
  function find_prime_values($prime_values, $input)     //$prime_values = list of prime numbers up to $input
  {
    echo "input: " . $input . "</br>";
    if(validate($input) == true)                        //first validate whether input is valid
    {
      array_push($prime_values, 2);                     //will always push 2 because validate() checks if input >= 2
      for($i = 3; $i <= $input; $i += 2)                //iterates through odd numbers (even numbers are not prime)
      {
        if(is_prime($i) == true)
        {
          array_push($prime_values, $i);                //if $i is a prime number, add $i to the end of the end of $prime_values
        }
      }
      print_prime_values($prime_values, $input);        //print the list of prime values
    }
  }

  //tests if $test_num is a prime number
  function is_prime($test_num)                          //$test_num = number being tested if prime
  {
    for($i = 2; $i <= $test_num/2; $i++)
    {
      if($test_num % $i == 0)
        return false;
    }
    return true;
  }

  //prints list of prime values up to input (includes input if it is prime number)
  function print_prime_values($prime_values, $input)
  {
    for($i = 0; $i <= count($prime_values) - 2; $i++)   //iterates through each value of $prime_values, except last value
    {
      echo current($prime_values) . ", ";               //print current value in $prime_values, with comma
      next($prime_values);                              //iterates to next value
    }
    echo end($prime_values) . "</br></br>";             //prints last value, without comma
  }

  //validates input
  //returns 'true' if valid input, 'false' if not
  function validate($input)
  {
    if(!is_int($input))                                 //if input is not an integer, echo error message
    {
      echo "'" . $input . "' is not a valid input. </br></br>";
      return false;
    }
    elseif($input < 2)                                  //if input is not >= 2, echo error message
    {
      echo $input . " is not a prime number. </br></br>";
      return false;
    }
    else                                                //passes validation, continues
    {
      return true;
    }
  }

  //tester function
  function tester()
  {
    echo "<b>Shayanna Gatchalian
    </br> CS 174
    </br> 9.11.2020 </b>
    </br></br> <b>Assignment #1</b> </br></br>";
    find_prime_values(array(), 2);
    find_prime_values(array(), 15);
    find_prime_values(array(), 100);
    find_prime_values(array(), 0);
    find_prime_values(array(), -1);
    find_prime_values(array(), "hello123");
  }
?>
