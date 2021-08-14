<?php
    $start = '0';
    $end = '10';
    
    for ($i = 1; $i <= 10; $i++) {
        $random_number = (string)rand(+$start, +$end); // Случайное число с типом string
        $last_digit = substr($random_number, -1, 1);
        $pre_last_digit = (strlen($random_number) > 1) ? substr($random_number,-2,1) : null;
        
        if ( $last_digit == '1' && $pre_last_digit != '1' ) {
            echo $random_number . ' груша' . "\n";
        } elseif ( in_array($last_digit, ['2','3','4']) && $pre_last_digit != '1' ) {
            echo $random_number . ' груши' . "\n";
        } else {
            echo $random_number . ' груш' . "\n";
        }
        
        $start = ($start == '0') ? '10': $start . '0';
        $end = $end . '0';
    }
?>

