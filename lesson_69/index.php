<?php
    $a = rand(0, 200);
    $b = rand(0, 200);
    
    echo 'a = ' . $a . ', b = ' . $b . "\n";
    
    $list = range($a, $b);
    
    foreach ($list as $value) {
        $string = (string)$value;
        $last_digit = substr($string, -1, 1);
        $pre_last_digit = (strlen($string) > 1) ? substr($string,-2,1) : null;
        
        if ( $last_digit == '1' && $pre_last_digit != '1' ) {
            echo $value . ' яблоко' . "\n";
        } elseif ( in_array($last_digit, ['2','3','4']) && $pre_last_digit != '1' ) {
            echo $value . ' яблока' . "\n";
        } else {
            echo $value . ' яблок' . "\n";
        }
    }
    
?>

