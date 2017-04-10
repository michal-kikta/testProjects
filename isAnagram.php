<?php

/**
 * @param $string1
 * @param $string2
 * @return bool
 *
 * isAnagram returns TRUE if two inputs strings are anagrams (they letters can be rearranged to form same word e.g.
 * "admirer" and "married")
 *
 * return FAlSE if strings are not anagrams or input is invalid
 *
 */

function isAnagram($string1, $string2) {

    if(empty($string1) || empty($string2)) {
        return false; // if one of input is empty it is considered to be invalid input
    }

    if(!is_string($string1) || !is_string($string2)) {
        return false; // input needs to be string
    }

    if(strlen($string1) != strlen($string2)) {
        return false; // two strings can't be  anagrams if they are not same length
    }

    // lowercase only characters will be better for this comparison
    $string1 = strtolower($string1);
    $string2 = strtolower($string2);

    $charComparisonArray = [];
    // make array for every character
    for($i = 0, $length = strlen($string1); $i<$length; $i++){

        $oneChar = $string1{$i};

        if(empty($charComparisonArray[$oneChar])) {
            $charComparisonArray[$oneChar] = 1;
        } else {
            $charComparisonArray[$oneChar]++;
        }
    }

    // let's compare second string, whether there are all characters from the first one
    for($i = 0, $length = strlen($string2); $i<$length; $i++){

        $oneChar = $string2{$i};

        if(empty($charComparisonArray[$oneChar])) {
            return false; // if there was no character in $string1 then it can't be Anagram
        } else {
            $charComparisonArray[$oneChar]--;
        }
    }

    // if it is anagram all fields must be 0
    foreach ($charComparisonArray as $char) {
        if(!empty($char)) {
            return false;
        }
    }

    //it looks like we passed all tests
    return true;
}

