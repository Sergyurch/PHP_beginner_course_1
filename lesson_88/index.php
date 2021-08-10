<?php
    $length = 13;
    $count = 5;
    $array = [];
    $letters = range('a','z');
    
    for ($i = 1; $i <= $count; $i++) {
        $string = '';
        
        while ( strlen($string) < $length ) {
           $string .=  $letters[array_rand($letters)];
        }
        
        $array[] = $string;
    }
   
    print_r($array);
    
    sort($array);
    print_r($array);
    
    foreach ($array as &$word) {
        $word = substr($word, 1);
    }
    
    print_r($array);
    
    rsort($array);
    print_r($array);
?>

